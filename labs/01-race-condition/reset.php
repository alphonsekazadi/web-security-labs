<?php

declare(strict_types=1);

$pdo = require __DIR__ . '/config.php';

$pdo->beginTransaction();

try {
    $pdo->exec("DELETE FROM transfers");

    $stmt = $pdo->prepare(
        "UPDATE accounts
         SET balance_cents = 10000
         WHERE id = 1"
    );

    $stmt->execute();

    $pdo->commit();

    echo "Account reset to 100.00 USD.\n";

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    throw $e;
}