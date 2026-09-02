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

## Phase 0 — Project Foundation

Status: **Completed**

Scope:

- Laravel 12 API project running through Docker.
- PostgreSQL development database.
- Next.js frontend integration.
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

Status: **In Progress**

Goal: establish the minimum domain required to sell one-time lifetime Course
access and track learning progress without real payment-provider or AWS video
integration.

### Slice 1.1 — Student Identity and Registration

Status: **Completed**

- Student profile attached 1:1 to User.
- Spatie `HasRoles` enabled on User.
- Initial `admin` and `student` roles.
- Registration creates User, assigns student role, creates Student, and issues
  a token inside one database transaction.
- PostgreSQL `students.user_id` unique constraint.

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

Status: **Completed — pending commit**

- Admin-only Role CRUD.
- Admin-only Permission CRUD.
- Assign and synchronize Roles for Users.
- Assign and synchronize Permissions for Roles.
- Protect management endpoints using Spatie authorization.
- Protect system roles from unsafe deletion or renaming.

### Slice 1.4 — Course Catalog

Status: **Planned**

- Courses.
- Chapters ordered by `sort_order`.
- Lessons ordered by `sort_order`.
- Publication state.
- Decimal Course price and currency.
- Private video storage keys and processing status only.
- Public read API for published Course content.

### Slice 1.5 — Payment Gateway Foundation

Status: **Planned**

- Safe gateway metadata: code, display name, and active state.
- No provider secrets stored in the database.
- No real Paymob or Fawry communication yet.

### Slice 1.6 — Orders

Status: **Planned**

- An Order represents a purchase by one User for one Course.
- Snapshot amount and currency at purchase time.
- Order lifecycle: pending, paid, cancelled, and refunded.

### Slice 1.7 — Payment Attempts

Status: **Planned**

- Multiple Payment attempts may belong to one Order.
- Payment belongs to one PaymentGateway.
- Payment lifecycle: pending, paid, failed, and refunded.
- Prepare identifiers required for future webhook idempotency.
- Never accept payment success from the frontend.

### Slice 1.8 — Enrollment and Course Access

Status: **Planned**

- Enrollment represents lifetime Course access.
- `UNIQUE(user_id, course_id)` prevents duplicate access records.
- Verified payment success atomically marks Payment paid, marks Order paid,
  and creates Enrollment.
- Backend authorization verifies Course ownership.

### Slice 1.9 — Devices

Status: **Planned**

- Register and identify user devices.
- Track last activity and revocation.
- Prepare Service-owned device-limit rules.

### Slice 1.10 — Lesson Progress

Status: **Planned**

- Track last position, last watched time, and completion.
- `UNIQUE(user_id, lesson_id)` prevents duplicate progress records.
- Course access is required before protected progress operations.

### Phase 1 Completion Criteria

- All Core tables and relationships exist in PostgreSQL.
- A Student can register and authenticate.
- A Student can verify email and securely reset a forgotten password.
- Admins can manage and assign Roles and Permissions.
- Published Course structure can be read.
- An Order can have multiple Payment attempts.
- Only verified successful payment processing can create Enrollment.
- A User cannot receive duplicate Enrollment for one Course.
- Enrolled users can access Course content and update Lesson progress.
- Device records can be created, updated, and revoked.

## Phase 2 — Real Payment Integrations

Status: **Planned**

Dependencies: Phase 1 Orders, PaymentGateways, Payments, and Enrollments.

Scope:

- First real payment provider integration selected by the project.
- Provider checkout/session creation.
- Signed webhook verification.
- Webhook idempotency.
- Provider transaction reconciliation.
- Retry-safe payment processing.
- Refund state synchronization where supported.

Implement one provider vertically before adding another provider.

## Phase 3 — Private Video Infrastructure

Status: **Planned**

Dependencies: Phase 1 Course access and Lessons.

Scope:

- Private S3 object storage.
- CloudFront private delivery.
- Signed URLs or signed cookies.
- Upload workflow.
- HLS transcoding and processing state.
- Access checks before issuing playback authorization.

Permanent public video URLs are not allowed.

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
- Reviews.
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
Production AWS/HLS work before Course access exists
```
