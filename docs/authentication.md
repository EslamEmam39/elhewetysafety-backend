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

Registration sends a short-lived email-verification OTP. The OTP must be
hashed at rest, single-use, purpose-specific, attempt-limited, and invalidated
when a replacement is issued.

## Email Verification

Users verify their email with an OTP sent to their registered address.

- Verification and resend endpoints must be rate limited.
- A successful verification sets `email_verified_at` once.
- Expired, consumed, or invalid OTPs must fail with a Business Exception.
- OTP values must never be stored in plain text or returned by the API.

## Forgotten Password

Password-reset requests must return the same public response whether or not the
email exists, preventing account enumeration.

Password reset requires the email, OTP, new password, and password
confirmation. Successful reset consumes the OTP and revokes existing Sanctum
tokens.

Email-verification and password-reset OTPs must have different purposes and
must not be interchangeable.

## Logout

Normal logout deletes only the current access token. Do not revoke every token
unless the requested feature explicitly logs out all devices.

## Authorization

Use `spatie/laravel-permission` with initial roles `admin` and `student`.

Backend authorization is mandatory; frontend guards provide UX only. Never
trust client-provided user IDs, roles, permissions, prices, ownership, payment
status, enrollment status, or course access.

Use Policies, permission middleware, ownership rules, or Services as needed.

Role and Permission management endpoints are admin-only. Spatie remains the
single source of truth; do not add role or permission columns to users.
