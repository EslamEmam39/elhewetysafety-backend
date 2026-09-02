# Database Rules

## Database

The project uses PostgreSQL. Do not introduce MySQL-specific SQL or assume
MySQL and PostgreSQL behave identically.

## General Rules

Use foreign keys, indexes, unique constraints, intentional nullability,
decimal money values, and transactions where required.

Important constraints:

```text
users.email UNIQUE
courses.slug UNIQUE
enrollments(user_id, course_id) UNIQUE
lesson_progress(user_id, lesson_id) UNIQUE
```

## Foreign Keys and Deletion

Choose delete behavior intentionally. Do not cascade every relationship by
default. Financial and access history involving Users, Orders, Payments, and
Enrollments must not disappear accidentally.

## Money

Use decimal values, never float or double. Orders store purchase price and
currency snapshots. Payments store the amount and currency for that attempt.

## Transactions

Use transactions for dependent writes. Verified payment completion must
atomically mark Payment paid, mark Order paid, and create Enrollment. A failure
must not leave a partially completed purchase or access state.

## PostgreSQL Verification

Validate migrations against the PostgreSQL Docker service. Do not claim a
migration is valid based only on SQLite or MySQL behavior.
