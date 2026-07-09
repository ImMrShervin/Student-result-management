# Contributing to SRMS

Thanks for your interest! Please follow the guidelines below to keep the codebase clean and consistent.

## Development workflow

1. Fork → branch off `develop` → work in `feature/<short-name>` or `fix/<short-name>`.
2. Run `composer install` and `npm install` in the respective directories.
3. Ensure `php artisan test` and `npm run build` pass.
4. Format with `./vendor/bin/pint` (Laravel Pint) and follow ESLint for TS.
5. Write/update tests for any business-logic change.
6. Open a PR to `develop` with a clear description and screenshots (if UI).

## Coding standards

**Backend**
- PSR-12, `declare(strict_types=1);` in every PHP file.
- Controllers stay thin — delegate to Services.
- Every mutation goes through a FormRequest.
- Every model has a Policy — never authorize inline.
- Use DTOs for cross-layer input; use Enums for finite states.
- Prefer `readonly` classes and constructor property promotion.

**Frontend**
- React function components + hooks only.
- Type-first: prefer `zod` schemas for runtime + inferred TS types.
- No inline styles — Tailwind utility classes only.
- API calls go through `src/lib/api.ts`; caching via TanStack Query.

## Commits

Follow **Conventional Commits**: `feat:`, `fix:`, `chore:`, `docs:`, `test:`, `refactor:`.

Example:
```
feat(enrollment): enforce max-credit cap per semester
```

## Reporting bugs

Include: Laravel/PHP version, browser, reproduction steps, expected vs. actual behavior, and a minimal failing test if possible.
