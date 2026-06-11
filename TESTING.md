# Testing Guide

This project has two parts:

- `school`: Laravel backend
- `rand`: Flutter frontend

## Backend: Laravel

Run all backend tests:

```powershell
cd school
php artisan test
```

Run tests by category:

```powershell
php artisan test --filter=PublicApiHealthTest
php artisan test --filter=ProtectedRoutesTest
php artisan test --filter=StudentWorkflowTest
php artisan test --filter=ResponseTimeTest
```

### Database Workflow Tests

`StudentWorkflowTest` runs realistic database scenarios inside transactions:

- create a temporary student user
- login and receive a Sanctum token
- fetch `/api/user`
- create and fetch exam scores
- create and fetch payments
- subscribe to transport and cancel the subscription

These tests use the current database connection from `school/.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_db
```

Start MySQL/MariaDB first, then run:

```powershell
cd school
php artisan test --filter=StudentWorkflowTest
```

If the database server is not running, these workflow tests are skipped instead of failing the whole test suite.

Run formatting checks:

```powershell
cd school
vendor\bin\pint --test
```

Run frontend asset build for the Laravel dashboard:

```powershell
cd school
npm.cmd run build
```

## Frontend: Flutter

Run Flutter tests:

```powershell
cd rand
flutter test
```

Run only model parsing tests:

```powershell
cd rand
flutter test test/models/model_parsing_test.dart
```

Run static analysis:

```powershell
cd rand
flutter analyze
```

## Current Test Coverage

- Functional tests validate health endpoints and request validation.
- Integration tests validate auth protection and route registration contracts.
- Performance tests validate lightweight endpoint response time.
- Flutter model tests validate parsing of realistic API payloads.
