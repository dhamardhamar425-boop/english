---
paths:
  - 'app/Http/Controllers/**/*.php'
---

# Controllers

## Base Controller uses AuthorizesRequests; role gating via route middleware
Laravel 13's base app/Http/Controllers/Controller.php is empty — add `use AuthorizesRequests;` so `$this->authorize()` works. Role gating happens at route level via `super_admin`/`org_admin` aliases in bootstrap/app.php, NOT via `$this->middleware()` in a constructor (API removed). Shared pages (dashboard, pendaftar) use only `auth` + controller query scoping + Policy; `super_admin`/`org_admin` middleware is reserved for role-exclusive pages.
