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

## Actual Experimental Results

### Vulnerable Implementation

Two concurrent requests attempted to transfer
80.00 USD from an account containing 100.00 USD.

Observed results:

- Both requests read the initial balance of 10,000 cents.
- Both passed the balance validation.
- Both transfers were accepted.
- The final account balance was -6,000 cents.
- Two transfer records were created.

### Secure Implementation

The same experiment was repeated using
a database transaction and SELECT ... FOR UPDATE.

Observed results:

- The first request acquired the row lock.
- The first request transferred 80.00 USD.
- The second request waited for the lock.
- The second request read the updated balance of 2,000 cents.
- The second transfer was rejected.
- The final account balance remained 2,000 cents.
- Only one transfer record was created.

### Conclusion

The experiment demonstrates how a race condition
can violate a business rule when balance validation
and account modification are not properly synchronized.

Pessimistic row locking, combined with database
transactions, prevents the demonstrated concurrent
transfers from independently validating the same
outdated account balance.