# MVC structure

## Current architecture

- `public/index.php` receives the request and delegates to the front controller.
- `app/Controllers/FrontController.php` resolves the route and dispatches the controller action.
- `app/Routing/Router.php` contains route matching logic, including static aliases and parameterized paths.
- `app/routes.php` is the single source of truth for web and API routes.
- `app/Controllers` contains application controllers grouped by web and API concerns.
- `app/Models` contains persistence-oriented classes.
- `app/Services` contains reusable business or infrastructure services.
- `app/Views` contains presentation templates only.

## Professional conventions adopted

- Special route parsing such as `detail/{detail_ref}` now lives in the router instead of the front controller.
- Reusable URL/detail helpers are centralized in bootstrap helpers to avoid duplicated slug logic.
- Controllers stay focused on orchestration, authorization and view/API responses.
- Deployment configuration is externalized through environment variables.

## Recommended next growth path

- Keep complex SQL and external API integration inside models/services, not in views or routing.
- Prefer adding new dynamic routes through `patterns` in `app/routes.php`.
- If API complexity keeps growing, introduce a shared `ApiController` base class for JSON responses and authorization guards.
