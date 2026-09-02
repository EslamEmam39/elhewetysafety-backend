# Backend Rules

## Project

- Laravel 12.
- PostgreSQL.
- JSON REST API consumed by a Next.js frontend.
- Authentication: Laravel Sanctum using Bearer Tokens.
- Authorization: `spatie/laravel-permission`.
- API routes belong in `routes/api.php`.
- Development runs through Docker Compose.

## Required Architecture

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

- Controllers remain thin.
- Validation belongs in FormRequests.
- Business logic belongs in Services.
- Eloquent queries and persistence belong in Repositories.
- Public API fields belong in API Resources.
- Expected domain failures use Business Exceptions.
- Do not add abstraction layers unless the current Core feature requires them.
- Do not create Repository interfaces without a real substitution need.

## Required Reading

Before implementing a feature, read:

- `docs/phases.md`
- `docs/architecture.md`
- `docs/core-domain.md`
- `docs/authentication.md`
- `docs/database.md`

## Implementation

1. Inspect the current implementation and relevant documentation.
2. Check whether migrations, models, and classes already exist.
3. Follow the nearest existing feature structure.
4. Do not recreate working code or migrations.
5. Preserve unrelated user changes.
6. Avoid overengineering.
7. Update the relevant documentation when a reusable architectural decision changes.

## Docker

Run backend framework and package commands inside the backend container.

```bash
docker compose exec backend php artisan migrate
docker compose exec backend php artisan route:list --path=api
docker compose exec backend composer require vendor/package
```

If `compose.yaml` is in the parent directory:

```bash
docker compose -f ../compose.yaml exec backend php artisan migrate
```

## Verification

- Check syntax after PHP changes.
- Run `php artisan route:list --path=api` when routes change.
- Validate migrations against PostgreSQL when migrations change.
- Do not claim a check passed unless it was executed.
- Do not create or run automated backend tests unless explicitly requested.
