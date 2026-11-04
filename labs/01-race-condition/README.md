# Lab 01 — Race Condition in a Banking Transfer

## Objective

Demonstrate how concurrent requests can bypass a business rule
when balance validation and account debiting are not properly
synchronized.

## Scenario

Alice has an account balance of 100.00 USD.

Two concurrent requests attempt to transfer 80.00 USD each.

The expected behavior is that only one transfer succeeds.

## Requirements

- PHP with PDO MySQL
- MySQL or MariaDB
- InnoDB storage engine

## Files

- `config.php`: Database connection.
- `config.local.php`: Local database credentials (not committed).
- `config.example.php`: Example local configuration.
- `database.sql`: Database schema.
- `reset.php`: Restore the initial account state.
- `vulnerable.php`: Intentionally vulnerable transfer implementation.

## Planned Experiments

1. Execute two concurrent transfers without locking.
2. Observe the resulting account balance.
3. Identify the race condition.
4. Implement pessimistic locking.
5. Compare vulnerable and secure implementations.

## Security Concepts

- Race Condition
- Time-of-Check to Time-of-Use (TOCTOU)
- Business Logic Vulnerability
- Database Transactions
- Pessimistic Locking

## Safety

This laboratory is intended exclusively for controlled local testing.