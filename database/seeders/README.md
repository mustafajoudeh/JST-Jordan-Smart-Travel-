# Local JST seed data

Run the curated local fixture set with:

```powershell
php artisan db:seed --class="Database\Seeders\DatabaseSeeder"
```

The main seeder exits outside `local` and `testing` environments. It uses stable `.test` account emails and `firstOrCreate` patterns, so rerunning it does not reset passwords or replace existing records. It does not create or modify a system administrator.

All demo accounts use the local-only password `JstLocal!2026` when first created:

| Email | Role |
| --- | --- |
| `demo.solo@jst.test` | traveler |
| `demo.couple@jst.test` | traveler |
| `demo.family@jst.test` | traveler |
| `demo.friends@jst.test` | traveler |
| `demo.budget@jst.test` | traveler |
| `demo.provider.heritage@jst.test` | provider |
| `demo.provider.guide@jst.test` | provider |
| `demo.provider.food@jst.test` | provider |

Demo providers and services remain unverified, pending, and unable to accept bookings. Events remain drafts without source URLs. The booking and payment seeders are intentionally not called: no provider is eligible for bookings and the application has no payment gateway. Media records are omitted because the repository has no local image files; `MediaFactory` requires an existing public-disk image.

The default Laravel seeder only inserts application reference/demo rows. It does not seed framework tables and does not reset the database. Never use these local credentials in a shared or production environment.
