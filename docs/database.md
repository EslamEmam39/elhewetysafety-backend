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
devices.uuid UNIQUE
targeted_professional_fields.code UNIQUE
courses.slug UNIQUE
chapters.code UNIQUE
lessons.code UNIQUE
countries.code UNIQUE
cities.code UNIQUE
students.user_id UNIQUE
course_categories.slug UNIQUE
payment_gateways.code UNIQUE
orders.code UNIQUE
payments.code UNIQUE
payments.idempotency_key UNIQUE
payments(payment_gateway_id, provider_reference) UNIQUE
enrollments.code UNIQUE
enrollments(user_id, course_id) UNIQUE
enrollments.payment_id UNIQUE where payment_id is not null
lesson_progress(user_id, lesson_id) UNIQUE
course_reviews.code UNIQUE
course_reviews(user_id, course_id) UNIQUE
course_reviews.rating CHECK 1..5
```

## User Identity

`users.id` remains the internal numeric primary key used by foreign keys.
`users.uuid` is the stable public identifier and must be unique. UUID columns
are reserved for Users and Devices: a Device UUID identifies the client
installation supplied during login. Other Domain entities expose stable codes
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

## Payment Gateways

`payment_gateways` is normalized reference data. Each row has a stable unique
`code`, localized `name_en` and `name_ar`, `is_active`, and `sort_order`. Provider
credentials and secrets are not database columns. Gateways are deactivated
instead of deleted so later Payment records can preserve their relationship.

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
`orders.amount` and `orders.currency` are copied from the Course by the backend
at creation time and are never accepted from client input. Orders use restricted
foreign keys to Users and Courses so purchase history cannot be cascaded away.
Payment attempts similarly snapshot amount and currency from their Order and use
restricted foreign keys to Orders and PaymentGateways. Provider references are
unique within their gateway, while server-generated idempotency keys are globally
unique.

## Enrollments

`enrollments` is the normalized User-to-Course access relation. It stores only
atomic source and lifecycle state, with a unique User/Course pair. Its optional,
unique `payment_id` is null for free access and preserves the exact paid attempt
that granted access. User, Course, and Payment foreign keys restrict deletion so
historical access cannot disappear through a cascade.

## Devices and Access Tokens

`devices` is normalized per User and has a globally unique UUID. Name,
platform, User-Agent, IP address, activity time, and revocation time depend only
on that Device row. The User foreign key cascades because Devices are
authentication artifacts, not financial or learning history.

The existing `personal_access_tokens` table gains a nullable `device_id`
foreign key in a separate migration. Null preserves tokens issued before this
feature. Deleting a Device nulls that reference, although normal Device removal
uses revocation and token deletion rather than physical deletion.

## Lesson Progress

`lesson_progress` is the normalized User-to-Lesson learning-state relation.
The unique `(user_id, lesson_id)` constraint is the final duplicate boundary;
position, watch time, and completion time depend on that whole relationship.
Both foreign keys cascade because progress is derived state whose owning User
or Lesson no longer exists. Numeric IDs remain internal and no extra UUID or
public code is introduced for a progress row.

## Lesson Video Metadata

A Lesson owns at most one active source video. Its disk, storage key, MIME type,
byte size, processing state, duration, and optional HLS manifest key are atomic
attributes that depend only on the Lesson, so they remain on `lessons` rather
than introducing a one-row video table. The storage key is provider-neutral;
permanent public URLs are neither stored nor exposed. Replacing a source video
updates this metadata and removes the superseded object after persistence.

## Course Reviews

`course_reviews` is the normalized User-to-Course rating relation. Rating,
optional text, and moderation time depend on the complete User/Course pair.
The database enforces one row per pair and ratings from 1 through 5. Reviews
use stable public codes while numeric IDs remain internal. Course aggregate
values are queried from Lessons, Enrollments, and visible Reviews and are not
stored as duplicated columns on Courses.

## Transactions

Use transactions for dependent writes. Verified payment completion must
atomically mark Payment paid, mark Order paid, and create Enrollment. A failure
must not leave a partially completed purchase or access state.

## PostgreSQL Verification

Validate migrations against the PostgreSQL Docker service. Do not claim a
migration is valid based only on SQLite or MySQL behavior.
