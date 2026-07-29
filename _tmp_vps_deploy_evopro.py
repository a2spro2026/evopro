#!/usr/bin/env python3
"""Deploy EvoPro to evopro.a2spr.com (VPS A2S — same stack as BatiXpert / YallahGo)."""
import os
import sys
import tarfile
import tempfile
import time
from pathlib import Path

import paramiko

sys.stdout.reconfigure(encoding="utf-8", errors="replace")

HOST = "51.255.162.99"
USER = "ubuntu"
PW = os.environ.get("VPS_SSH_PASSWORD")
if not PW:
    raise SystemExit("Set VPS_SSH_PASSWORD before running deploy (same as YallahGo / BatiXpert).")
FQDN = "evopro.a2spr.com"
SLUG = "evopro"
ROOT = f"/var/www/{FQDN}"
APP = ROOT
LOCAL = Path(__file__).resolve().parent

EXCLUDE_DIRS = {
    ".git",
    "node_modules",
    "vendor",
    ".cursor",
    "storage/logs",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
}
EXCLUDE_FILES = {
    ".env",
    ".env.local",
    ".env.production",
    "_tmp_vps_deploy_evopro.py",
    "deploy-evopro.bat",
    "evopro_data.json",
}
EXCLUDE_PREFIXES = ("_tmp_vps_", "_tmp_")


def should_exclude(path: Path, root: Path) -> bool:
    rel = path.relative_to(root)
    rel_posix = rel.as_posix()
    for d in EXCLUDE_DIRS:
        if rel_posix == d or rel_posix.startswith(d + "/"):
            return True
    if path.name in EXCLUDE_FILES:
        return True
    if any(path.name.startswith(p) for p in EXCLUDE_PREFIXES):
        return True
    # Never ship local app data over production data.
    if rel_posix == "storage/app/evopro_data.json" or rel_posix.endswith("/evopro_data.json"):
        return True
    return False


def make_tarball() -> Path:
    tmp = tempfile.NamedTemporaryFile(suffix=".tar.gz", delete=False)
    tmp.close()
    tar_path = Path(tmp.name)
    with tarfile.open(tar_path, "w:gz") as tar:
        for item in LOCAL.rglob("*"):
            if should_exclude(item, LOCAL):
                continue
            if item.is_file():
                tar.add(item, arcname=item.relative_to(LOCAL).as_posix())
    return tar_path


def run(client, cmd: str, t: int = 900, check: bool = True) -> tuple[int, str]:
    print(f"\n>>> {cmd[:280]}", flush=True)
    _stdin, stdout, stderr = client.exec_command(cmd, timeout=t, get_pty=True)
    out = stdout.read().decode("utf-8", "replace")
    err = stderr.read().decode("utf-8", "replace")
    code = stdout.channel.recv_exit_status()
    text = (out + "\n" + err).strip()
    if text:
        print(text[-6000:], flush=True)
    if check and code != 0:
        raise RuntimeError(f"Command failed ({code}): {cmd[:120]}")
    return code, text


def write_production_env(client) -> None:
    env = f"""APP_NAME=EvoPro
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://{FQDN}

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE={APP}/database/database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
CACHE_STORE=file
QUEUE_CONNECTION=sync

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
"""
    run(
        client,
        f"cat > {APP}/.env <<'EOF'\n{env}\nEOF",
    )


def main() -> None:
    print(f"Deploying EvoPro to {FQDN} ...", flush=True)
    tar_path = make_tarball()
    remote_tar = f"/tmp/evopro_deploy_{int(time.time())}.tar.gz"

    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(HOST, username=USER, password=PW, timeout=30, allow_agent=False, look_for_keys=False)

    try:
        run(
            client,
            f"test -d {ROOT} && echo EXISTS || echo '{PW}' | sudo -S new-site {SLUG} php",
            check=False,
        )
        run(client, f"mkdir -p {APP}")
        run(
            client,
            f"echo '{PW}' | sudo -S chown -R ubuntu:www-data {ROOT} && "
            f"echo '{PW}' | sudo -S chmod -R g+w {ROOT}",
            check=False,
        )

        sftp = client.open_sftp()
        print(f"Uploading archive -> {remote_tar}", flush=True)
        sftp.put(str(tar_path), remote_tar)
        sftp.close()

        _, probe = run(client, f"test -f {APP}/.env && echo HAS_ENV || echo NO_ENV", check=False)
        has_env = "HAS_ENV" in probe

        run(client, f"cp {APP}/.env /tmp/evopro_env_backup 2>/dev/null || true", check=False)
        run(
            client,
            f"cp {APP}/storage/app/evopro_data.json /tmp/evopro_data_backup 2>/dev/null || true",
            check=False,
        )
        run(client, f"cd {APP} && tar -xzf {remote_tar}")
        run(client, f"rm -f {remote_tar}")

        if has_env:
            run(client, f"cp /tmp/evopro_env_backup {APP}/.env", check=False)
        else:
            write_production_env(client)

        run(
            client,
            f"mkdir -p {APP}/storage/app && "
            f"test -f /tmp/evopro_data_backup && "
            f"cp /tmp/evopro_data_backup {APP}/storage/app/evopro_data.json || true",
            check=False,
        )

        run(
            client,
            f"mkdir -p {APP}/database {APP}/storage/framework/{{cache,sessions,views}} "
            f"{APP}/storage/logs {APP}/bootstrap/cache && "
            f"touch {APP}/database/database.sqlite",
        )
        run(client, f"cd {APP} && composer install --no-dev --optimize-autoloader --no-interaction", t=1200)

        _, key_probe = run(client, f"grep '^APP_KEY=' {APP}/.env | cut -d= -f2-", check=False)
        if not key_probe.strip():
            run(client, f"cd {APP} && php artisan key:generate --force")
        run(client, f"cd {APP} && php artisan storage:link || true", check=False)
        run(client, f"cd {APP} && php artisan migrate --force")
        run(
            client,
            f"cd {APP} && php artisan config:cache && php artisan route:cache && php artisan view:cache",
            check=False,
        )
        run(
            client,
            f"echo '{PW}' | sudo -S chown -R ubuntu:www-data {APP} && "
            f"echo '{PW}' | sudo -S chmod -R ug+rwx {APP}/storage {APP}/bootstrap/cache {APP}/database",
        )
        run(
            client,
            f"echo '{PW}' | sudo -S certbot --nginx -d {FQDN} --non-interactive "
            f"--agree-tos -m admin@a2spr.com --redirect || echo CERTBOT_SKIP",
            check=False,
            t=300,
        )
        _, status = run(
            client,
            f"curl -s -o /dev/null -w '%{{http_code}}' https://{FQDN}/ || "
            f"curl -s -o /dev/null -w '%{{http_code}}' http://{FQDN}/",
            check=False,
        )
        print(f"\nHTTP(S) status: {status.strip()}", flush=True)
        print(f"\nDone: https://{FQDN}", flush=True)
    finally:
        client.close()
        try:
            tar_path.unlink(missing_ok=True)
        except OSError:
            pass


if __name__ == "__main__":
    main()
