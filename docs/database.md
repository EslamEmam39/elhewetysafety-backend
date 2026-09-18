# Database Rules

## Database

The project uses PostgreSQL. Do not introduce MySQL-specific SQL or assume
MySQL and PostgreSQL behave identically.

## General Rules

Use foreign keys, indexes, unique constraints, intentional nullability,
decimal money values, and transactions where required.

## Normalization

Review every new or changed schema against 1NF, 2NF, and 3NF before writing its
migration.

- 1NF: columns contain atomic values and relationships are not stored as CSV,
  numbered columns, or queryable arrays.
- 2NF: every non-key column in a table with a composite key depends on the whole
  key, not only part of it.
- 3NF: non-key columns depend only on the row key. Reusable reference data is
  moved to its owning table and referenced by a foreign key.

Normalize domain-managed lists such as countries, cities, course categories,
targeted professional fields, and payment gateways. Do not store their display
names repeatedly on Student, User, Course, Order, or Payment rows.

Normalization is not a reason to remove historical snapshots. Orders, payments,
invoices, and certificates may copy values that must remain unchanged after the
source record is edited.

## Migration Organization

Each project Domain table has one dedicated create-table migration. Do not create
multiple Domain tables in the same migration. Alterations to an existing table,
including adding a foreign key after its referenced table exists, use a separate
migration ordered after both dependencies. Framework and vendor-published
migrations may retain their upstream structure.

Important constraints:

```text
users.uuid UNIQUE
users.username UNIQUE
users.phone UNIQUE
users.email UNIQUE
targeted_professional_fields.code UNIQUE
courses.slug UNIQUE
chapters.code UNIQUE
lessons.code UNIQUE
countries.code UNIQUE
cities.code UNIQUE
students.user_id UNIQUE
course_categories.slug UNIQUE
enrollments(user_id, course_id) UNIQUE
lesson_progress(user_id, lesson_id) UNIQUE
```

## User Identity

`users.id` remains the internal numeric primary key used by foreign keys.
`users.uuid` is the stable public identifier and must be unique. Within domain
tables, UUID columns are reserved for Users; other entities expose stable codes
or slugs. Framework infrastructure tables may keep identifiers required by
Laravel itself.
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

## Student Profile and Locations

Student-specific profile fields belong to `students`, not `users`. A Student may
reference a normalized City; a City belongs to one Country.

```text
Country 1 ── M Cities
City    1 ── M Students
```

Use `students.city_id` rather than a free-text `students.city`. Countries and
cities use stable codes, localized names, active state, and deterministic sort
order. Existing profiles may keep `city_id` nullable until their location is
selected or migrated.
Referenced Countries and Cities are deactivated instead of deleted. Deactivating
a Country also deactivates its Cities while preserving Student relationships.

## Foreign Keys and Deletion

Choose delete behavior intentionally. Do not cascade every relationship by
default. Financial and access history involving Users, Orders, Payments, and
Enrollments must not disappear accidentally.

`courses.course_category_id` and `courses.instructor_id` are nullable only for
existing Course rows during rollout. New Course API input requires an active
Category; Instructor is an optional existing User. Category and Instructor
deletion is restricted while referenced. Chapters and Lessons use globally
unique public codes while retaining numeric foreign keys internally.

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
