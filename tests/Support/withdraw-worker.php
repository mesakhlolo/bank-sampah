<?php

/*
 * Test worker for ConcurrentWithdrawalTest.
 *
 * Boots a standalone Laravel application against a file-backed SQLite database,
 * rendezvouses with its sibling worker on a filesystem barrier, then calls the
 * real WithdrawalService. Each worker prints one JSON result so the test can
 * assert that exactly one of two simultaneous withdrawals succeeded.
 *
 * This is test support only; it is never loaded by the application.
 */

use App\Models\User;
use App\Services\WithdrawalService;
use Illuminate\Support\Facades\DB;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if ($argc < 7) {
    fwrite(STDERR, 'usage: withdraw-worker.php <barrier> <worker> <database> <customer> <user> <amount>');

    exit(2);
}

[, $barrierDir, $workerId, $database, $customerId, $userId, $amount] = $argv;

// Point the default connection at the shared file and wait rather than fail
// instantly when the sibling transaction holds the write lock.
config([
    'database.connections.sqlite.database' => $database,
    'database.connections.sqlite.busy_timeout' => 5000,
]);

DB::purge('sqlite');

file_put_contents($barrierDir.DIRECTORY_SEPARATOR.'ready-'.$workerId, '1');

$deadline = microtime(true) + 10;

while (count(glob($barrierDir.DIRECTORY_SEPARATOR.'ready-*')) < 2) {
    if (microtime(true) > $deadline) {
        fwrite(STDERR, 'barrier timeout');

        exit(2);
    }

    usleep(1000);
}

$result = ['ok' => false, 'error' => 'not started'];

try {
    app(WithdrawalService::class)->createWithdrawal([
        'customer_id' => (int) $customerId,
        'amount' => (float) $amount,
        'transaction_date' => date('Y-m-d'),
    ], User::findOrFail((int) $userId));

    $result = ['ok' => true];
} catch (Throwable $exception) {
    $result = [
        'ok' => false,
        'class' => $exception::class,
        'error' => $exception->getMessage(),
    ];
}

fwrite(STDOUT, json_encode($result));
