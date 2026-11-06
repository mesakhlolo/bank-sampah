<?php

use App\Models\Deposit;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/*
 * Module 5 — real concurrency proof.
 *
 * Unlike the sequential double-spend test in BalanceIntegrityTest, this test
 * runs two actual PHP processes against the same file-backed SQLite database,
 * synchronises them on a filesystem barrier, and makes both attempt a
 * withdrawal at the same moment. Exactly one must win; the loser must be
 * rejected after re-reading the committed balance, leaving no negative ledger.
 */

test('two concurrent withdrawal processes cannot overdraw the ledger', function () {
    $database = storage_path('framework/testing/withdrawal-concurrency-'.Str::uuid().'.sqlite');
    $barrier = storage_path('framework/testing/withdrawal-barrier-'.Str::uuid());

    if (! is_dir(dirname($database))) {
        mkdir(dirname($database), 0777, true);
    }

    touch($database);
    mkdir($barrier, 0777, true);

    config(['database.connections.sqlite_concurrency' => [
        'driver' => 'sqlite',
        'database' => $database,
        'prefix' => '',
        'foreign_key_constraints' => true,
        'busy_timeout' => 5000,
        'journal_mode' => null,
        'synchronous' => null,
        'transaction_mode' => 'DEFERRED',
    ]]);

    // The Telescope migration resolves its connection from this config value,
    // which defaults to DB_CONNECTION (sqlite). Point it at the file so it does
    // not collide with the in-memory schema created by RefreshDatabase.
    config(['telescope.storage.database.connection' => 'sqlite_concurrency']);

    try {
        Artisan::call('migrate', [
            '--database' => 'sqlite_concurrency',
            '--force' => true,
        ]);

        $connection = DB::connection('sqlite_concurrency');
        $now = now()->toDateTimeString();

        $userId = $connection->table('users')->insertGetId([
            'name' => 'Operator Uji',
            'email' => 'concurrency@example.test',
            'password' => bcrypt('password'),
            'role' => 'operator',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $customerId = $connection->table('customers')->insertGetId([
            'member_code' => 'CUST-9999',
            'name' => 'Nasabah Uji',
            'joined_at' => now()->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $connection->table('ledger_entries')->insert([
            'customer_id' => $customerId,
            'ref_type' => Deposit::class,
            'ref_id' => 1,
            'direction' => 'KREDIT',
            'amount' => 10000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $processes = collect([1, 2])->map(function (int $worker) use ($barrier, $database, $customerId, $userId) {
            $process = new Process([
                PHP_BINARY,
                base_path('tests/Support/withdraw-worker.php'),
                $barrier,
                (string) $worker,
                $database,
                (string) $customerId,
                (string) $userId,
                '6000',
            ], base_path());

            $process->setTimeout(30);
            $process->start();

            return $process;
        });

        $processes->each->wait();

        $results = $processes->map(function (Process $process): array {
            $output = trim($process->getOutput());

            preg_match('/\{.*\}/', $output, $matches);

            if ($matches === []) {
                return [
                    'ok' => false,
                    'error' => 'no JSON output. stdout: '.$output.' stderr: '.$process->getErrorOutput(),
                ];
            }

            return json_decode($matches[0], true) ?? ['ok' => false, 'error' => 'invalid JSON'];
        });

        $successes = $results->filter(fn (array $result) => $result['ok'] ?? false)->count();

        $balance = (float) $connection->table('ledger_entries')
            ->where('customer_id', $customerId)
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'KREDIT' THEN amount ELSE -amount END), 0) as balance")
            ->value('balance');

        $debits = $connection->table('ledger_entries')
            ->where('customer_id', $customerId)
            ->where('direction', 'DEBIT')
            ->count();

        expect($successes)->toBe(1, 'worker results: '.$results->toJson())
            ->and($balance)->toBe(4000.0)
            ->and($balance)->toBeGreaterThanOrEqual(0)
            ->and($debits)->toBe(1);
    } finally {
        DB::purge('sqlite_concurrency');

        foreach (glob($barrier.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($barrier);
        @unlink($database);
        @unlink($database.'-journal');
        @unlink($database.'-wal');
        @unlink($database.'-shm');
    }
});
