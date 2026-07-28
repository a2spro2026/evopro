@echo off
cd /d "%~dp0"
if not defined VPS_SSH_PASSWORD (
  echo Set VPS_SSH_PASSWORD in your environment before deploying.
  echo Example: set VPS_SSH_PASSWORD=your-password
  pause
  exit /b 1
)
echo Deploying EvoPro to evopro.a2spr.com ...
python _tmp_vps_deploy_evopro.py
if errorlevel 1 exit /b 1
echo.
pause
