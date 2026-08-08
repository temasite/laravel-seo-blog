# laravel-seo-blog

Laravel blog with SEO and an RSS feed

## Creating a user

Before creating a user, run the migrations and seed the roles and permissions:

```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

Run the command interactively:

```bash
php artisan user:create
```

The command prompts for the user's name, email address, role, and password. The password input is hidden and must be confirmed. New users are always created with an active status.

You can also provide the name, email address, and role as options:

```bash
php artisan user:create \
  --name="Admin User" \
  --email="admin@example.com" \
  --role=admin
```

Available roles:

- `admin` — full access;
- `manager` — can manage articles and categories, but cannot manage users or settings.

The password intentionally cannot be passed as a command-line option, preventing it from being stored in shell history or exposed in the process list.
