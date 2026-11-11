<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientSequencer
{
    public static function resequenceIfNeeded(): bool
    {
        if (! Schema::hasTable('clients')) {
            return false;
        }

        $ids = DB::table('clients')->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();
        if ($ids === []) {
            return false;
        }

        foreach ($ids as $index => $id) {
            if ($id !== $index + 1) {
                return self::resequence();
            }
        }

        return false;
    }

    public static function resequence(): bool
    {
        if (! Schema::hasTable('clients')) {
            return false;
        }

        $clients = DB::table('clients')->orderBy('id')->get();
        if ($clients->isEmpty()) {
            return false;
        }

        $map = [];
        $next = 1;
        foreach ($clients as $client) {
            $map[(int) $client->id] = $next++;
        }

        $changed = false;
        foreach ($map as $old => $new) {
            if ($old !== $new) {
                $changed = true;
                break;
            }
        }
        if (! $changed) {
            return false;
        }

        $driver = Schema::getConnection()->getDriverName();

        DB::transaction(function () use ($map, $driver) {
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF');
            } elseif ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
            }

            foreach ($map as $old => $new) {
                $temp = 1_000_000 + $old;
                self::rewriteId($old, $temp, null);
            }

            foreach ($map as $old => $new) {
                $temp = 1_000_000 + $old;
                $code = 'CC-'.str_pad((string) $new, 4, '0', STR_PAD_LEFT);
                self::rewriteId($temp, $new, $code);
            }

            $max = count($map);
            if ($driver === 'sqlite') {
                try {
                    DB::table('sqlite_sequence')->where('name', 'clients')->delete();
                    DB::table('sqlite_sequence')->insert(['name' => 'clients', 'seq' => $max]);
                } catch (\Throwable) {
                    // sqlite_sequence is absent until the first autoincrement write
                }
                DB::statement('PRAGMA foreign_keys = ON');
            } elseif ($driver === 'mysql') {
                DB::statement('ALTER TABLE clients AUTO_INCREMENT = '.($max + 1));
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        });

        return true;
    }

    private static function rewriteId(int $from, int $to, ?string $code): void
    {
        DB::table('clients')->where('id', $from)->update(['id' => $to]);

        if (Schema::hasTable('delivery_orders')) {
            $payload = ['client_id' => $to];
            if ($code !== null) {
                $payload['client_code'] = $code;
            }
            DB::table('delivery_orders')->where('client_id', $from)->update($payload);
        }

        if (Schema::hasTable('client_payments')) {
            DB::table('client_payments')->where('client_id', $from)->update(['client_id' => $to]);
        }
    }
}
