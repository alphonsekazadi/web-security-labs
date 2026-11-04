<?php

declare(strict_types=1);

$pdo = require __DIR__ . '/config.php';

$accountId = 1;
$amount = isset($argv[1])
    ? filter_var($argv[1], FILTER_VALIDATE_INT)
    : 8000;

if ($amount === false || $amount <= 0) {
    fwrite(STDERR, "Invalid transfer amount.\n");
    exit(1);
}

$pid = getmypid();

echo "[PID $pid] Starting secure transfer...\n";

try {
    // 1. Start the transaction.
    $pdo->beginTransaction();

    // 2. Lock the account row.
    $stmt = $pdo->prepare(
        "SELECT balance_cents
         FROM accounts
         WHERE id = ?
         FOR UPDATE"
    );

    $stmt->execute([$accountId]);

    $account = $stmt->fetch();

    if (!$account) {
        throw new RuntimeException("Account not found.");
    }

    $balance = (int) $account['balance_cents'];

    echo "[PID $pid] Locked account. Balance: {$balance} cents.\n";

    // 3. Enforce the business rule.
    if ($amount > $balance) {
        $pdo->rollBack();

        echo "[PID $pid] Transfer rejected: insufficient balance.\n";
        exit(0);
    }

    // 4. Artificial delay to demonstrate lock contention.
    sleep(2);

    // 5. Debit the account.
    $stmt = $pdo->prepare(
        "UPDATE accounts
         SET balance_cents = balance_cents - ?
         WHERE id = ?"
    );

    $stmt->execute([$amount, $accountId]);

    // 6. Record the transfer.
    $stmt = $pdo->prepare(
        "INSERT INTO transfers (sender_id, amount_cents)
         VALUES (?, ?)"
    );

    $stmt->execute([$accountId, $amount]);

    // 7. Commit all changes atomically.
    $pdo->commit();

    echo "[PID $pid] Transfer accepted: {$amount} cents.\n";

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, "[PID $pid] Transfer failed: {$e->getMessage()}\n");
    exit(1);
}