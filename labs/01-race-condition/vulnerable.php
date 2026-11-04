<?php

declare(strict_types=1);

$pdo = require __DIR__ . '/config.php';

$accountId = 1;
$amount = isset($argv[1]) ? filter_var($argv[1], FILTER_VALIDATE_INT) : 8000;

if ($amount === false || $amount <= 0) {
    fwrite(STDERR, "Invalid transfer amount.\n");
    exit(1);
}

echo "[PID " . getmypid() . "] Starting transfer...\n";

// 1. Read the current balance.
$stmt = $pdo->prepare(
    "SELECT balance_cents FROM accounts WHERE id = ?"
);
$stmt->execute([$accountId]);

$account = $stmt->fetch();

if (!$account) {
    fwrite(STDERR, "Account not found.\n");
    exit(1);
}

$balance = (int) $account['balance_cents'];

echo "[PID " . getmypid() . "] Balance read: {$balance} cents.\n";

// 2. Check the business rule.
if ($amount > $balance) {
    echo "[PID " . getmypid() . "] Transfer rejected: insufficient balance.\n";
    exit(0);
}

echo "[PID " . getmypid() . "] Balance check passed.\n";

// 3. Artificial delay to expose the race condition.
sleep(2);

// 4. Debit the account.
$stmt = $pdo->prepare(
    "UPDATE accounts
     SET balance_cents = balance_cents - ?
     WHERE id = ?"
);
$stmt->execute([$amount, $accountId]);

// 5. Record the transfer.
$stmt = $pdo->prepare(
    "INSERT INTO transfers (sender_id, amount_cents)
     VALUES (?, ?)"
);
$stmt->execute([$accountId, $amount]);

echo "[PID " . getmypid() . "] Transfer accepted: {$amount} cents.\n";