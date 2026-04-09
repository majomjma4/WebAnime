# Deployment Guide

## Supported layouts

This project is prepared for two deployment styles:

1. VPS or subdomain with document root pointing to `public/`
2. cPanel or shared hosting with document root pointing to the project root

## Environment file

Create `.env` from `.env.example` and set:

```ini
APP_ENV=production
APP_BASE_URL=https://your-domain.com
APP_BASE_PATH=

DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_user
DB_PASS=your_password
```

Use `APP_BASE_PATH=/subfolder` only if the app is served from a URL like `https://your-domain.com/subfolder`.

## Option 1: VPS or document root to `public/`

Point your web server root to:

```text
/path/to/WebAnime/public
```

### Apache vhost example

```apache
DocumentRoot /var/www/WebAnime/public

<Directory /var/www/WebAnime/public>
    AllowOverride All
    Require all granted
</Directory>
```

### Nginx example

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/WebAnime/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }
}
```

Set:

```ini
APP_BASE_URL=https://your-domain.com
APP_BASE_PATH=
```

## Option 2: cPanel or shared hosting from project root

If you cannot point the domain to `public/`, upload the full project and keep the domain/subdomain pointing to the project root.

This repo now includes:

- root `index.php`
- root `.htaccess`

Those files proxy requests into `public/` so routes and assets still work.

Set:

```ini
APP_BASE_URL=https://your-domain.com
APP_BASE_PATH=
```

If deployed in a subfolder:

```ini
APP_BASE_URL=https://your-domain.com/subfolder
APP_BASE_PATH=/subfolder
```

## Pre-deploy checklist

1. Upload the full project, including `app/`, `public/`, and `.htaccess`
2. Create `.env`
3. Import the database
4. Confirm PHP version supports typed properties and nullsafe operators
5. Ensure Apache mod_rewrite is enabled, or use the provided Nginx config
6. Make sure outbound HTTPS requests are allowed for Jikan and Google Translate endpoints

## Notes

- Cookies and CSRF are now scoped using the configured base path
- Frontend helpers can read the server-provided base path automatically
- For best results on VPS, prefer pointing the document root directly to `public/`
