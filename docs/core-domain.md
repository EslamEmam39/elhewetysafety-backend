# Core Course Platform Domain

Phase 1 focuses only on the Core System.

## Main Entities

```text
users
students
countries
cities
targeted_professional_fields
course_categories
courses
chapters
lessons
orders
payment_gateways
payments
enrollments
devices
lesson_progress
```

## Users and Students

`User` is the authentication identity and owns Spatie roles and permissions.
Do not add a duplicate role column.

`User` keeps a numeric internal primary key for database relationships and a
unique UUID for public references. Its identity fields are unique `username`,
`phone`, and `email`. Localized names are stored as `full_name_en` and
`full_name_ar`; there is no generic `name` field.

Account moderation state is stored in `is_banned` with an optional
`ban_reason`. Ban enforcement belongs in the authentication and authorization
application flows rather than in controllers.

Admins may ban or unban Users. Banned Users cannot log in or use authenticated
API routes, and receive a `403` response containing the stored reason when one
is present. Administrators cannot ban themselves.

Admins may list, search, filter, view, and update User identity data by public
UUID. Changing a User email clears `email_verified_at`. User deletion is not part
of administration because historical financial and learning relationships must
be preserved.

`Student` is a 1:1 profile attached to User. A User may exist without a Student
profile, for example an Admin. Students do not have separate authentication.
Administrators may list, view, update, and delete Student profiles by the owning
User UUID. Deleting a Student profile removes the `student` role but preserves
the User authentication identity and its history.

Student-specific optional profile data includes avatar storage key, job title,
company, and biography. Location is normalized: `students.city_id` references a
City, and each City belongs to a Country. Do not store a reusable city or country
name directly on Student.
Only active Countries and Cities are offered publicly. Referenced locations are
deactivated rather than deleted, and profile updates reject inactive selections.

Initial roles are `admin` and `student`.

## Targeted Professional Fields

A TargetedProfessionalField is reference data selected by a User during
registration. One field may be selected by many Users, while each registered
User selects one field.

Only active fields are offered publicly and accepted for new registrations.
Fields have localized English and Arabic names, a stable unique code, and
`sort_order` for deterministic display. UUIDs are reserved for Users.

## Course Content

A Course is the purchased product. Core fields include title, slug,
description, decimal price, currency, level, thumbnail, publication state, and
publication time. A Course belongs to one CourseCategory for catalog filtering.
The instructor relationship must be finalized before the Course Catalog slice
is implemented; instructor identity reuses User authentication through nullable
`courses.instructor_id` rather than creating a second login system. Existing
Courses may remain uncategorized or without an instructor during migration;
new Courses require an active CourseCategory.

```text
Course 1 ── M Chapters
Chapter 1 ── M Lessons
CourseCategory 1 ── M Courses
```

Chapters and Lessons use `sort_order`.
Courses expose `slug`; Chapters and Lessons expose globally unique stable
`code` values. Their numeric primary keys remain internal.

Lessons store private storage keys rather than permanent public URLs:
`video_s3_key`, `video_hls_manifest_key`, `video_status`, `duration_seconds`,
and `is_free_preview`.

Possible video states are `pending`, `processing`, `ready`, and `failed`.
Private S3, CloudFront, HLS processing, and full AWS integration are outside
the current Core phase.

## Orders

An Order represents a purchase and belongs to one User and Course. It snapshots
the purchase `amount` and `currency`; historical values do not change when the
Course price changes.

Orders expose a stable unique `code`; their numeric IDs remain internal. Order
creation accepts only a Course slug, and the backend snapshots price and currency
from a currently published paid Course. Repeated creation for the same User and
Course returns the existing pending Order. Free Courses bypass Orders and are
handled by the Enrollment flow. Students may cancel only their own pending
Orders. Administrators can inspect Orders but cannot manually force paid or
refunded states.

Possible statuses are `pending`, `paid`, `cancelled`, and `refunded`.

## Payment Gateways and Payments

PaymentGateway stores a stable unique code, localized English and Arabic names,
active state, and deterministic sort order. Only active gateways are exposed by
the public lookup API. Admin deletion deactivates a gateway so future Payment
history can continue referencing it. Provider secrets belong in environment or
secret configuration and must never be stored in this table.

A Payment is one payment attempt, not the purchase itself. An Order may have
multiple Payments, and each Payment belongs to a PaymentGateway.
Each attempt exposes a stable code and snapshots amount and currency from its
Order. Server-generated idempotency keys and gateway-scoped provider references
prepare for retry-safe webhook processing. Core creation records a pending
attempt only; provider initiation remains unavailable until Phase 2.

Never trust payment success sent by the frontend. Only verified backend or
provider communication may mark it paid. The design must support webhook
idempotency later.

Possible statuses are `pending`, `paid`, `failed`, and `refunded`.

## Enrollments

Enrollment represents Course access through a many-to-many relationship
between Users and Courses.

```text
UNIQUE(user_id, course_id)
```

A student enrolls in a Course once. Access is lifetime without subscriptions or
expiration. A paid Course creates Enrollment only after verified successful
payment. A free Course may create Enrollment directly without an Order or
Payment. Possible states are `active` and `revoked`; source is `free` or
`payment`. Each Enrollment exposes a stable code while its numeric key remains
internal. A payment-backed Enrollment references exactly one successful Payment.

The public Course catalog does not expose Chapters or Lessons. Protected Course
content is returned only to the owning User while their Enrollment is active.
Revocation removes content access without deleting access history. Re-enrolling
in a free Course is idempotent and does not bypass an administrative revocation.

## Purchase Flow

```text
Student
→ Course
→ Create Order
→ Choose Payment Gateway
→ Create Payment Attempt
→ Verified successful payment
→ Payment = paid
→ Order = paid
→ Create Enrollment
→ Lifetime Course Access
```

```text
Order = Purchase
Payment = Payment attempt
Enrollment = Course access
```

For a free Course (`price = 0`), the flow is:

```text
Student
→ Published free Course
→ Create Enrollment
→ Lifetime Course Access
```

## Devices

A Device represents one client installation used to authenticate a User. It
belongs to one User and exposes its UUID as the stable public identity. Device
name and optional platform come from the client; IP address and User-Agent are
captured by the backend. Last activity and revocation are tracked without using
browser fingerprinting as proof of identity.

Each Sanctum access token may reference one Device. Login registers or refreshes
the Device and binds the newly issued token to it. Revoking a Device sets
`revoked_at` and deletes every token currently bound to that Device. A later
successful credential login may trust and reactivate that same Device UUID.
Device-count limits belong in `DeviceService` but are not enforced until the
product selects a concrete limit.

## Lesson Progress

LessonProgress belongs to one User and Lesson and requires:

```text
UNIQUE(user_id, lesson_id)
```

Use `last_position_seconds`, `completed_at`, and `last_watched_at`.

Progress can be read or updated only while the User has an active Enrollment
for the Lesson's Course. One row represents one User/Lesson pair. Playback
position may move forward or backward for resume behavior but cannot exceed a
known Lesson duration. Completion is monotonic: setting `is_completed` records
`completed_at` once, and ordinary progress updates cannot undo completion.

## Course Reviews and Course Aggregates

A CourseReview belongs to one User and one Course. Only a User with an active
Enrollment may create or update a review, and `UNIQUE(user_id, course_id)`
limits that User to one rating per Course. Rating is an integer from 1 through
5; written review text is optional.

Students may delete their own review. Administration hides inappropriate
reviews using `hidden_at` instead of deleting them. Public lists, averages,
and counts include visible reviews only.

Course summary values are derived rather than duplicated: duration is the sum
of Lesson durations, Lesson count comes from Lessons, active Student count from
active Enrollments, and rating aggregates from visible CourseReviews.

## Outside the Current Core Phase

Do not automatically build real Paymob or Fawry integration, production
webhooks, full AWS/HLS infrastructure, quizzes, certificates, reviews,
coupons, subscriptions, instructor marketplace, advanced analytics, or an
affiliate system.
