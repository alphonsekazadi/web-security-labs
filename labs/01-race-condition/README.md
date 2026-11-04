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

## Experimental Results

### Vulnerable Implementation

Two concurrent requests attempted to transfer 80.00 USD
from an account containing 100.00 USD.

Observed results:

- Both requests passed the balance validation.
- Both transfers were accepted.
- The final account balance was -60.00 USD.
- Two transfer records were created.

### Secure Implementation

The secure implementation uses:

- PDO database transactions.
- InnoDB pessimistic row locking.
- SELECT ... FOR UPDATE.
- Server-side balance validation.
- Atomic transaction commit and rollback.

Expected results:

- One transfer is accepted.
- The second transfer is rejected.
- The final account balance is 20.00 USD.
- Only one transfer record is created.

### Conclusion

The vulnerability was caused by a race condition
between balance validation and account modification.

Pessimistic locking ensures that concurrent transactions
cannot independently validate and debit the same account
using the same outdated balance.