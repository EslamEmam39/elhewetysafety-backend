# Project Phases

This file is the source of truth for the project roadmap and current progress.
Every agent must read it before planning or implementing a feature.

## Status Legend

```text
Completed   Finished and verified.
In Progress Current implementation phase.
Planned     Approved direction, not started.
Deferred    Intentionally outside the current roadmap scope.
```

Do not begin a later phase when its required foundation is missing. Complete
work vertically and in dependency order. Update this file when a slice starts,
finishes, or changes scope.

A Slice moves to `Completed` in the same turn after its implementation,
PostgreSQL migration where applicable, route registration, Postman
synchronization, and required documentation have been verified. Keep
`In Progress` only when an explicit item in that Slice remains unfinished.

## Phase 0 — Project Foundation

Status: **Completed**

Scope:

- Laravel 12 API project running through Docker.
- PostgreSQL development database.
- Nuxt frontend integration.
- Laravel Sanctum Bearer Token authentication.
- Spatie roles and permissions package installation.
- Layered request architecture:

```text
Route
→ Controller
→ FormRequest
→ Service
→ Repository
→ Model / Database
→ Resource
→ ApiResponse
```

- Unified API success and error responses.
- Business Exception foundation.
- Authentication endpoints: register, login, logout, and current user.
- Architecture and domain documentation.

## Phase 1 — Core System

Status: **Completed**

Goal: establish the minimum domain required to sell one-time lifetime Course
access and track learning progress without real payment-provider or AWS video
integration.

### Slice 1.1 — Student Identity and Registration

Status: **Completed**

- Student profile attached 1:1 to User.
- Spatie `HasRoles` enabled on User.
- Initial `admin` and `student` roles.
- Registration creates User, assigns student role, and creates Student inside
  one database transaction. Authentication tokens are issued only after email
  verification and successful login.
- PostgreSQL `students.user_id` unique constraint.

### Slice 1.1a — Targeted Professional Fields

Status: **Completed**

- Localized professional-field reference data.
- Public list of active fields for registration.
- Registration requires one active field and stores it on the User.
- Existing Users remain nullable during migration.

### Slice 1.2 — Complete Authentication

Status: **Completed**

- Send an email-verification OTP after registration.
- Resend email-verification OTP with throttling.
- Verify email using a short-lived, single-use OTP.
- Request password reset without exposing whether an email exists.
- Reset password using a short-lived, single-use OTP.
- Invalidate previous OTPs when issuing a replacement.
- Limit failed OTP attempts.

### Slice 1.3 — Roles and Permissions Administration

Status: **Completed**

- Admin-only Role CRUD.
- Admin-only Permission CRUD.
- Assign and synchronize Roles for Users.
- Assign and synchronize Permissions for Roles.
- Protect management endpoints using Spatie authorization.
- Laravel Policies map admin operations to granular Spatie permissions.
- Protect system roles from unsafe deletion or renaming.
- Dedicated admin login endpoint reusing the shared authentication flow.
- Admin User listing, filtering, detail, and identity update by public UUID.
- User deletion is excluded to preserve future financial and learning history.

### Slice 1.3a — User Moderation

Status: **Completed**

- Admin-only ban and unban operations by User UUID.
- Required ban reason.
- Login and authenticated-route enforcement for banned Users.
- Administrators cannot ban themselves.

### Slice 1.3b — Student Profile and Location Reference Data

Status: **Completed**

- Normalized Countries and Cities reference data.
- A City belongs to one Country; a Student references one optional City.
- Student profile fields for avatar storage key, job title, company, and biography.
- Public active Country and City lookup APIs for profile forms.
- Admin management APIs deactivate referenced locations instead of deleting them.
- Admin Student profile listing, detail, update, and deletion by User UUID.

### Slice 1.4 — Course Catalog

Status: **Completed**

- Normalized Course categories.
- Courses linked to one category.
- Finalize the instructor profile and Course relationship without duplicate
  authentication.
- Chapters ordered by `sort_order`.
- Lessons ordered by `sort_order`.
- Publication state.
- Course level: beginner, intermediate, or advanced.
- Decimal Course price and currency.
- Private video storage keys and processing status only.
- Public read API for published Course content.
- Admin CRUD for categories, Courses, Chapters, and Lessons.
- Admin-only course-cover upload with validated JPG, PNG, or WebP media and a
  public presentation URL; storage keys remain internal.
- Global administration lists for Chapters and Lessons include their normalized
  parent references, while creation remains scoped to Course or Chapter.
- Lesson administration can reassign an existing Lesson to another Chapter by
  stable Chapter code while preserving the normalized foreign-key relationship.
- Spatie-backed Policies protect every administration operation.

### Slice 1.5 — Payment Gateway Foundation

Status: **Completed**

- Normalized gateway metadata: stable code, localized names, active state, and sort order.
- Public active gateway lookup API.
- Admin CRUD with deactivation instead of deletion.
- Spatie-backed Policy authorization and seeded permissions.
- No provider secrets stored in the database.
- No real Paymob or Fawry communication yet.

### Slice 1.6 — Orders

Status: **Completed**

- An Order represents a purchase by one User for one Course.
- Snapshot amount and currency at purchase time.
- Order lifecycle: pending, paid, cancelled, and refunded.
- Students can create, list, view, and cancel their own pending Orders.
- Reuse an existing pending Order for the same User and Course.
- Admins can filter and inspect Orders without manually forcing financial states.

### Slice 1.7 — Payment Attempts

Status: **Completed**

- Multiple Payment attempts may belong to one Order.
- Payment belongs to one PaymentGateway.
- Payment lifecycle: pending, paid, failed, and refunded.
- Prepare identifiers required for future webhook idempotency.
- Never accept payment success from the frontend.
- Core APIs create and inspect attempts without contacting providers.
- Gateway selection is resolved through the approved Interface and Factory.

### Slice 1.8 — Enrollment and Course Access

Status: **Completed**

- Enrollment represents lifetime Course access.
- `UNIQUE(user_id, course_id)` prevents duplicate access records.
- Paid-Course Enrollment can be created only by the verified payment
  completion workflow implemented in Phase 3; no client endpoint can forge it.
- Published free Courses create Enrollment directly without an Order or Payment.
- Backend authorization verifies Course ownership.
- Student APIs list and inspect owned Enrollments and expose Course content only
  while the Enrollment is active.
- Admin APIs inspect, revoke, and reactivate access through Policy permissions.
- The Core access foundation is complete; the atomic paid transition remains
  explicitly scoped to Phase 3 provider integration.

### Slice 1.9 — Devices

Status: **Completed**

- Register and identify user devices.
- Track last activity and revocation.
- Prepare Service-owned device-limit rules.
- Require device UUID, name, and optional platform during User and Admin login.
- Bind each newly issued Sanctum token to its Device.
- Allow authenticated Users to list and revoke their own Devices.
- Allow authorized Admins to inspect User Devices and revoke their tokens.
- A numeric device limit is intentionally not enforced until its product rule is approved.

### Slice 1.10 — Lesson Progress

Status: **Completed**

- Track last position, last watched time, and completion.
- `UNIQUE(user_id, lesson_id)` prevents duplicate progress records.
- Course access is required before protected progress operations.
- Students can list their saved progress for one enrolled Course.
- Progress updates reject playback positions beyond a known Lesson duration.
- Completion is monotonic: once completed, a later update does not clear it.

### Slice 1.11 — Course Reviews

Status: **Completed**

- Active enrolled Students can create one rating per Course.
- Students can read, update, and delete their own review.
- Public APIs expose only visible reviews.
- Admins can inspect, hide, and republish reviews through Policy permissions.
- Course duration, Lesson count, active Student count, average rating, and
  review count are computed from their normalized source tables.

### Phase 1 Completion Criteria

- All Core tables and relationships exist in PostgreSQL.
- A Student can register and authenticate.
- A Student can verify email and securely reset a forgotten password.
- Admins can manage and assign Roles and Permissions.
- Published Course structure can be read.
- An Order can have multiple Payment attempts.
- No frontend or public client endpoint can create paid-Course Enrollment;
  verified payment completion is the Phase 3 integration boundary.
- A published free Course can create Enrollment without an Order or Payment.
- A User cannot receive duplicate Enrollment for one Course.
- Enrolled users can access Course content and update Lesson progress.
- Device records can be created, updated, and revoked.
- Enrolled Students can rate Courses and public Course summaries expose review aggregates.

## Phase 2 — Video Infrastructure

Status: **In Progress**

Dependencies: Phase 1 Course access, Lessons, Enrollments, Devices, and Lesson
Progress.

Scope:

- Storage abstraction that supports a local development disk and production
  object storage without changing Lesson or playback business logic.
- One free Lesson video as the first vertical playback slice.
- Admin upload workflow with processing state and persisted storage keys only.
- Private S3-compatible object storage for production Course videos.
- CloudFront or equivalent CDN delivery.
- Short-lived signed URLs or signed cookies for protected playback.
- Access checks before issuing playback authorization.
- HLS manifest storage and transcoding/processing state.
- Free Course videos may use public CDN delivery only when intentionally public;
  the preferred default is the same protected delivery pipeline used by paid
  Courses.
- Paid Course videos always require an active Enrollment and temporary signed
  playback authorization.

Permanent public URLs for paid Course videos are not allowed. Laravel must not
stream production video bytes through the API process.

### Slice 2.1 — First Free Video

Status: **Completed**

- Configure the development storage disk.
- Upload one video through an authorized Admin endpoint.
- Attach its storage key, duration, and processing state to one Lesson.
- Authorize an enrolled Student to request playback metadata.
- Play the Lesson from Nuxt and persist Lesson Progress.
- Allow authorized administrators to preview an uploaded Lesson video through a
  temporary playback URL without exposing its storage key.
- Keep storage-specific URLs out of the database and public Resources.

Backend status: the local upload endpoint, provider-neutral Lesson metadata,
active-Enrollment playback authorization, and temporary playback URL contract
are implemented, the PostgreSQL migration has been applied, and deterministic
Admin, Student, free Course, Lesson, and active Enrollment values are available
in the canonical Postman collection. Re-seeding preserves uploaded video
metadata. A sample video has been uploaded and verified through the protected
playback endpoint. Local signed delivery supports HTTP byte ranges required for
seeking and resume, while production delivery remains delegated to object
storage/CDN temporary URLs. Nuxt requests the temporary playback URL, restores an
incomplete saved position, and persists bounded progress during playback, on
pause, before lesson changes, and on completion.

### Slice 2.2 — Production Private Delivery

Status: **Planned**

- Configure S3-compatible private storage and CDN delivery.
- Generate short-lived playback authorization.
- Add HLS processing and callback/status handling.
- Apply the same protected pipeline to free and paid content unless a free
  Lesson is explicitly approved for public CDN access.

## Phase 3 — Real Payment Integrations

Status: **In Progress**

Dependencies: Phase 1 Orders, PaymentGateways, Payments, and Enrollments.

Scope:

- First real payment provider integration selected by the project.
- Provider checkout/session creation.
- Signed webhook verification.
- Webhook idempotency.
- Provider transaction reconciliation.
- Retry-safe payment processing.
- Refund state synchronization where supported.
- Provider selection through `PaymentGatewayInterface`, concrete provider
  services, `PaymentGatewayFactory`, and the orchestrating `PaymentService`.

Implement one provider vertically before adding another provider.

### Slice 3.1 — Cash Payment Confirmation

Status: **In Progress**

- `cash` is an active normalized payment-gateway code and is resolved through
  `PaymentGatewayInterface` and `PaymentGatewayFactory`.
- Students create a pending Cash Payment attempt and receive a manual-confirmation
  next action; this does not grant Course access.
- An administrator with `payments.confirm-cash` can confirm only a pending Cash
  Payment whose Order is still pending.
- Confirmation atomically marks the Payment and Order paid and creates the
  payment-backed active Enrollment. The Payment stores the confirming User for
  financial audit history.
- PostgreSQL migration is applied. Remaining before this slice can become
  Completed: exercise the seeded/Postman Cash confirmation flow.

### Slice 3.2 — Paymob Checkout and Webhook

Status: **Completed**

- Backend creates Paymob Intention checkout sessions using server-only credentials.
- A public HMAC-verified transaction callback reconciles the saved provider order
  reference, amount, and currency, then completes the Order, Payment, and
  Enrollment atomically through `PaymentService`.
- A real sandbox callback through the public tunnel has verified the resulting
  paid Order, paid Payment, and payment-backed active Enrollment.

## Phase 4 — Learning Completion

Status: **Planned**

Dependencies: Phase 1 Course content, Enrollment, and Lesson progress.

Potential scope, implemented only after explicit approval:

- Course completion rules.
- Quizzes and attempts.
- Completion certificates.
- Certificate verification.

## Phase 5 — Administration and Platform Growth

Status: **Planned**

Dependencies: stable Core, payment, and learning workflows.

Potential scope, implemented as separately approved slices:

- Admin management APIs.
- Operational dashboards and analytics.
- Coupons and promotions.
- Notifications.
- Advanced account-sharing controls.

## Deferred Until Explicitly Approved

```text
Subscriptions
Instructor marketplace
Affiliate system
Advanced analytics
Multiple payment providers at the same time
```
