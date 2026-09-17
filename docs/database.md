# Database Rules

## Database

The project uses PostgreSQL. Do not introduce MySQL-specific SQL or assume
MySQL and PostgreSQL behave identically.

## General Rules

Use foreign keys, indexes, unique constraints, intentional nullability,
decimal money values, and transactions where required.

Important constraints:

```text
users.uuid UNIQUE
users.username UNIQUE
users.phone UNIQUE
users.email UNIQUE
targeted_professional_fields.uuid UNIQUE
targeted_professional_fields.code UNIQUE
courses.slug UNIQUE
enrollments(user_id, course_id) UNIQUE
lesson_progress(user_id, lesson_id) UNIQUE
```

## User Identity

`users.id` remains the internal numeric primary key used by foreign keys.
`users.uuid` is the stable public identifier and must be unique.
Existing rows are backfilled by migration. New Users, and existing Users saved
without a UUID, receive one through `UserObserver`.

Users have unique `username`, `phone`, and `email` values. The legacy `name`
column is not used; localized names are stored explicitly in `full_name_en`
and `full_name_ar`.

Account moderation uses `is_banned`, which defaults to `false`, and an
optional `ban_reason`. Storing these fields does not by itself enforce a ban;
authentication and authorization flows must explicitly reject banned users.

`users.targeted_professional_field_id` references
`targeted_professional_fields.id`. It is nullable only to preserve existing
accounts during migration; registration requires an active field. Deletion is
restricted while Users reference the field, so fields should be deactivated
instead of removed.

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
