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
app/Http/Controllers/Api
app/Http/Requests/{Feature}
app/Http/Resources
app/Services
app/Repositories
app/Exceptions/{Feature}
app/Traits
```

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

## Models

Models contain relationships, casts, attributes, and small model-specific
behavior. Multi-model workflows do not belong in Models.

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
