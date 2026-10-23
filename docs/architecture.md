# Backend Architecture

## Request and Response Flow

```text
Route
→ Controller
→ FormRequest
→ Service
→ Repository
→ Model / Database
```

```text
Service Result
→ Controller
→ API Resource
→ ApiResponse
→ JSON
```

## Directories

```text
app/Http/Controllers/Api/{Feature}
app/Http/Controllers/Api/Admin/{Feature}
app/Http/Requests/{Feature}
app/Http/Resources/{Feature}
app/Services/{Feature}
app/Repositories/{Feature}
app/Exceptions/{Feature}
app/Traits
```

Public and authentication API routes are defined in `routes/api.php`.
Controllers are grouped by feature. Public controllers live under
`app/Http/Controllers/Api/{Feature}`, while admin-only controllers live under
`app/Http/Controllers/Api/Admin/{Feature}`. Do not place new feature controllers
directly in the `Api` or `Admin` root when a feature directory applies.
Services and Repositories follow the same feature grouping under
`app/Services/{Feature}` and `app/Repositories/{Feature}`. Public and admin
operations for the same domain feature share that feature directory; the class
name may retain an `Admin` prefix when its behavior is administration-specific.
FormRequests and API Resources are also grouped by feature under
`app/Http/Requests/{Feature}` and `app/Http/Resources/{Feature}`. Shared base
classes such as `ApiFormRequest` remain at the layer root.
Admin-only API routes are defined in `routes/admin.php`, imported by
`routes/api.php`, and grouped under `/api/admin` with `auth:sanctum`,
`not_banned`, and `role:admin` middleware.

## Controllers

Controllers may receive validated FormRequests, read the authenticated user,
call Services, wrap returned data in Resources, and return unified responses.

Controllers must not contain validation rules, Eloquent queries, password or
token logic, payment or enrollment logic, transactions, or large business rules.

## FormRequests

Every endpoint accepting input uses a dedicated FormRequest extending
`App\Http\Requests\ApiFormRequest`. FormRequests validate input structure;
business rules belong in Services.

## Services

Services own application and business logic. They coordinate Repositories,
authorization rules, transactions, and external integrations. Services do not
return HTTP responses.

Use database transactions when multiple related writes must succeed or fail
together.

## Repositories

Repositories own Eloquent queries and persistence. Use intention-revealing
methods such as `findUserByEmail`, `createOrder`, and `createEnrollment`.

Do not create Repository interfaces automatically. Add one only when multiple
implementations or real substitution is required.

## Payment Provider Abstraction

Real payment-provider integrations use one explicit interface with interchangeable
service implementations:

```text
PaymentGatewayInterface
        ↑
CashService / PaymobService / FawryService
        ↑
PaymentGatewayFactory
        ↑
PaymentService
```

`CashService`, `PaymobService`, and `FawryService` implement
`PaymentGatewayInterface`.
`PaymentGatewayFactory` selects the implementation from the gateway's stable
code, and `PaymentService` owns orchestration, persistence coordination, and
transactions. Provider services do not create Orders, update Enrollments, or
construct HTTP responses. Provider secrets remain in environment-backed config.
The Cash implementation returns a next-action instruction and has no external
webhook. Its authorized Admin confirmation calls PaymentService, which locks the
Payment and Order and atomically records Payment paid, Order paid, and a
payment-backed Enrollment. Paymob creates its checkout Intention from the
backend and accepts payment completion only through an HMAC-verified public
server-to-server callback; the browser redirect is UX only. Fawry remains a
separate provider implementation in Phase 3.

Enrollment access follows the standard feature pipeline. `EnrollmentService`
owns free-course eligibility, idempotent access creation, and administrative
state transitions; `EnrollmentRepository` owns scoped persistence queries.
The public Course catalog loads metadata only, while the protected content route
loads Chapters and Lessons after the Service verifies an active Enrollment.

## Models

Models contain relationships, casts, attributes, and small model-specific
behavior. Multi-model workflows do not belong in Models.

## Observers

Observers may enforce model lifecycle invariants that must apply regardless of
which Service or Repository creates the model. `UserObserver` assigns a UUID
when a User is created or saved without one. Database migrations remain
responsible for backfilling existing rows.

## API Resources

Never expose Eloquent models directly through the public API. Resources define
public fields and must not expose passwords, secrets, provider credentials, or
sensitive payment data.

## Business Exceptions

Expected business failures use feature-specific exceptions under directories
such as `app/Exceptions/Auth`, `Course`, `Payment`, and `Enrollment`.

They extend `App\Exceptions\BusinessException` and are thrown by Services. The
global renderer in `bootstrap/app.php` converts them into unified JSON errors.
Controllers do not catch each exception unless recovery is endpoint-specific.

Authenticated routes use the `not_banned` middleware after `auth:sanctum` so
account moderation is enforced consistently outside individual controllers.

Device-aware authentication uses an application `PersonalAccessToken` model
extending Sanctum's token model. `AuthService` coordinates credential checks,
Device registration, token issuance, and token-to-Device association inside a
transaction. Device persistence and revocation remain in `DeviceRepository`.
The API middleware updates server-observed Device activity after authenticated
requests; tokens created before the Device feature remain valid with a null
Device relation.

Lesson Progress follows the standard feature pipeline. `LessonProgressService`
enforces active Enrollment access, duration bounds, and monotonic completion;
`LessonProgressRepository` owns scoped reads and the single User/Lesson
upsert. Controllers never accept a User identity or completion timestamp.

Course Reviews follow the feature pipeline. `CourseReviewService` enforces
published Course state, active Enrollment, ownership, and one-review rules;
`CourseReviewRepository` owns review queries and moderation persistence.
Course catalog queries calculate duration, Lesson count, active Enrollment
count, and visible-review aggregates in SQL without loading every related row
or storing duplicate summary columns.

Lesson video delivery follows the same layered flow. The Admin upload endpoint
passes the validated file to `VideoService`; storage operations stay in the
Service while `VideoRepository` owns Lesson metadata persistence and Enrollment
access queries. Playback responses expose a short-lived URL derived from the
stored disk and key. For the local development disk, that signed URL targets a
stream Service that supports HTTP byte ranges so browsers can seek and resume.
Production object storage still returns its provider/CDN temporary URL;
Controllers and Resources never expose a permanent storage URL, and Laravel
does not stream production video bytes through the API process.
The Admin preview endpoint is protected by `LessonPolicy` and reuses the same
readiness, storage-existence, and temporary-URL generation rules without an
Enrollment requirement. The administration client never receives a storage key.

Curriculum administration exposes global Chapter and Lesson lists for the
independent dashboard modules while preserving the nested create contracts.
Repositories eager-load the owning Course or Chapter/Course context, Resources
expose only stable codes, slugs, and titles, and the same Policies protect both
global and parent-scoped reads. Lesson updates may accept a stable
`chapter_code`; the Service resolves it through the Repository to the internal
`chapter_id` and rejects duplicate lesson positions within the selected Chapter.

## Policies and Spatie Permissions

Admin routes keep the `role:admin` middleware as the coarse entry boundary.
Controllers authorize each resource action through Laravel Policies. Policies
use Spatie `checkPermissionTo` for fine-grained decisions; Controllers and
Services do not hard-code permission checks. Services retain domain invariants
such as preventing self-ban even when a Policy has already authorized access.

Controllers call the matching Policy ability before invoking Services, including
custom operations such as ban, unban, role assignment, and permission
synchronization.

## Unified API Responses

Success:

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {}
}
```

Error:

```json
{
  "success": false,
  "message": "Operation failed",
  "errors": {}
}
```

Use appropriate HTTP status codes such as `200`, `201`, `204`, `400`, `401`,
`403`, `404`, `409`, and `422`. Services must not construct API responses.
