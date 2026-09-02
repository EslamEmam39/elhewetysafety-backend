# Authentication and Authorization

## Authentication

Use Laravel Sanctum Bearer Tokens. The Next.js frontend authenticates against
the Laravel JSON API.

```text
Email + Password
→ FormRequest validation
→ AuthService
→ Repository finds User
→ Password verification
→ Sanctum Personal Access Token
→ Token returned to frontend
```

Protected requests send `Authorization: Bearer TOKEN`, and protected backend
routes use `auth:sanctum`.

## Login Architecture

Authentication logic does not belong in AuthController.

```text
AuthController
→ LoginRequest
→ AuthService
→ Repository
→ User
```

Token creation belongs to the authentication application layer.

## Registration

Registration validates input, creates the User, assigns the `student` role,
creates a Student profile when required, and returns a public Resource.

User creation, role assignment, and Student creation must use a database
transaction when they form one operation.

## Logout

Normal logout deletes only the current access token. Do not revoke every token
unless the requested feature explicitly logs out all devices.

## Authorization

Use `spatie/laravel-permission` with initial roles `admin` and `student`.

Backend authorization is mandatory; frontend guards provide UX only. Never
trust client-provided user IDs, roles, permissions, prices, ownership, payment
status, enrollment status, or course access.

Use Policies, permission middleware, ownership rules, or Services as needed.
