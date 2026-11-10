# Student Lost & Found System

Laravel application for the CvSU Student Lost & Found system. Admin and student pages use Blade views, Vite, and Tailwind CSS.

## Run locally

Requirements: PHP 8.3 or newer, Composer, and Node.js with npm.

After cloning the repository, get the latest `main` branch and run:

```bash
git switch main
git pull --ff-only origin main
composer run setup
composer run dev
```

`composer run setup` installs PHP and JavaScript dependencies, creates the local `.env` and SQLite database when needed, runs migrations, and builds the frontend assets. `composer run dev` starts the Laravel development server and Vite. Open `http://127.0.0.1:8000` and sign in with an account in that local database.

If the page still shows an old design, stop the dev process, pull `main` again, then run:

```bash
php artisan optimize:clear
npm run build
composer run dev
```

The generated `public/build` files and `.env` are intentionally not committed. Each teammate must build assets on their own machine. Their local database and login accounts are also separate unless they configure the same database connection; do not share a populated `.env` containing database credentials in GitHub.

To confirm a checkout has the current admin UI commit, run:

```bash
git log -1 --oneline
```

It should show `4078d12 Use consistent admin sidebar across pages` or a newer commit.
