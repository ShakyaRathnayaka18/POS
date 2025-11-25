# Apache Subdirectory Setup for Multiple Laravel Apps

## Overview
This document explains how to configure Apache to serve multiple Laravel applications using Alias directives on the same EC2 instance.

## Problem
- Laravel project (HMART) in `/var/www/html/`
- Need to access them via subdirectories: `http://IP/HMART` and `http://IP/TEMPLATE`
- Laravel's routing was conflicting with Apache Alias configuration

## Solution Architecture

### 1. Apache Configuration (`/etc/apache2/sites-available/000-default.conf`)

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    # Root redirects to HMART
    RedirectMatch ^/$ /HMART

    # === HMART Main Project ===
    Alias /HMART /var/www/html/HMART/public
    <Directory /var/www/html/HMART/public>
        AllowOverride All
        Require all granted
    </Directory>

    # === SPARE_TEMPLATE Project ===
    Alias /TEMPLATE /var/www/html/SPARE_TEMPLATE/public
    <Directory /var/www/html/SPARE_TEMPLATE/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

**Key Points:**
- Use `Alias` to map URL paths to physical directories
- DocumentRoot is `/var/www/html` (parent directory)
- Each project's `public` folder is aliased
- Root `/` redirects to `/HMART`

### 2. Laravel `.htaccess` Configuration

**HMART (`/var/www/html/HMART/public/.htaccess`):**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Set base for alias
    RewriteBase /HMART

    # Exclude /TEMPLATE alias from all rewrite rules
    RewriteCond %{REQUEST_URI} ^/TEMPLATE
    RewriteRule ^ - [L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**SPARE_TEMPLATE (`/var/www/html/SPARE_TEMPLATE/public/.htaccess`):**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Important:**
- **DO NOT** use `RewriteBase /TEMPLATE` - causes double prefix issues
- HMART excludes `/TEMPLATE` requests to prevent conflicts
- Keep standard Laravel rewrite rules

### 3. Laravel Environment Configuration

**HMART (`.env`):**
```env
APP_URL=http://65.1.107.36/HMART
SESSION_PATH=/HMART
SESSION_DRIVER=database
```

**SPARE_TEMPLATE (`.env`):**
```env
APP_URL=http://65.1.107.36
SESSION_PATH=/TEMPLATE
SESSION_DRIVER=database
```

**Critical:**
- `SESSION_PATH` must be unique for each app to prevent session conflicts
- HMART uses full URL with `/HMART` prefix
- SPARE_TEMPLATE uses base URL without prefix (to avoid double prefix)

### 4. Laravel Routes Configuration

**HMART (`routes/web.php`):**
```php
Route::redirect('/', '/HMART/dashboard', 301);

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth redirects
return redirect()->intended('/HMART/dashboard');
return redirect('/HMART/login');
```

**SPARE_TEMPLATE (`routes/web.php`):**
```php
Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth redirects - NO prefix needed
return redirect()->intended('/dashboard');
return redirect('/login');
```

**Key Difference:**
- HMART routes include `/HMART` prefix in redirects
- SPARE_TEMPLATE routes use relative paths (Apache Alias handles prefix)

## Common Issues & Solutions

### Issue 1: Class "Native\Electron\ElectronServiceProvider" not found
**Cause:** NativePHP packages for desktop apps deployed to web server
**Solution:**
```bash
# Remove desktop app files
rm -rf /var/www/html/SPARE_TEMPLATE/dist
rm -rf /var/www/html/SPARE_TEMPLATE/vendor/nativephp
rm /var/www/html/SPARE_TEMPLATE/config/nativephp.php
composer dump-autoload
```

### Issue 2: Double URL Prefix (`/TEMPLATE/TEMPLATE/...`)
**Cause:**
- `RewriteBase /TEMPLATE` in `.htaccess`
- `APP_URL=http://IP/TEMPLATE` in `.env`
- Middleware forcing root URL with prefix

**Solution:**
- Remove `RewriteBase` from `.htaccess`
- Set `APP_URL=http://IP` (no prefix)
- Don't use URL forcing middleware

### Issue 3: Permission Denied Errors
**Cause:** Wrong file ownership/permissions
**Solution:**
```bash
sudo chown -R www-data:www-data /var/www/html/SPARE_TEMPLATE/storage
sudo chown -R www-data:www-data /var/www/html/SPARE_TEMPLATE/bootstrap/cache
sudo chmod -R 775 /var/www/html/SPARE_TEMPLATE/storage
sudo chmod -R 775 /var/www/html/SPARE_TEMPLATE/bootstrap/cache
```

### Issue 4: Session Conflicts Between Apps
**Cause:** Both apps using `SESSION_PATH=/`
**Solution:**
```env
# HMART .env
SESSION_PATH=/HMART

# SPARE_TEMPLATE .env
SESSION_PATH=/TEMPLATE
```

### Issue 5: Redirects Going to Wrong App
**Cause:** Apache catching requests before Alias processes them
**Solution:**
- Disable conflicting virtual hosts: `sudo a2dissite HMART.conf`
- Use single virtual host with multiple Alias directives
- Check active sites: `apache2ctl -S`

## Deployment Checklist

1. **Apache Configuration:**
   - [ ] Create/update virtual host config
   - [ ] Set up Alias directives for each app
   - [ ] Disable conflicting virtual hosts
   - [ ] Test config: `apache2ctl configtest`
   - [ ] Restart Apache: `sudo systemctl restart apache2`

2. **Laravel Configuration:**
   - [ ] Update `.env` with correct `APP_URL`
   - [ ] Set unique `SESSION_PATH` for each app
   - [ ] Update routes with correct prefixes
   - [ ] Update auth controller redirects
   - [ ] Configure `.htaccess` correctly

3. **Permissions:**
   - [ ] Set ownership: `chown -R www-data:www-data storage bootstrap/cache`
   - [ ] Set permissions: `chmod -R 775 storage bootstrap/cache`

4. **Cache Clearing:**
   - [ ] `php artisan config:clear`
   - [ ] `php artisan route:clear`
   - [ ] `php artisan cache:clear`
   - [ ] `php artisan optimize:clear`
   - [ ] Remove: `bootstrap/cache/*.php`

5. **Remove Desktop App Files:**
   - [ ] Delete `dist/` folder
   - [ ] Delete `vendor/nativephp/`
   - [ ] Delete `config/nativephp.php`
   - [ ] Delete `app/Providers/NativeAppServiceProvider.php`
   - [ ] Run `composer dump-autoload`

## Testing

**URL Structure:**
- `http://65.1.107.36/` → Redirects to `/HMART`
- `http://65.1.107.36/HMART` → HMART application
- `http://65.1.107.36/HMART/login` → HMART login page
- `http://65.1.107.36/TEMPLATE` → SPARE_TEMPLATE application
- `http://65.1.107.36/TEMPLATE/login` → SPARE_TEMPLATE login page

## Important Notes

1. **Never use both `RewriteBase` and prefixed `APP_URL`** - causes double prefix
2. **Each app needs unique `SESSION_PATH`** - prevents session conflicts
3. **Remove all NativePHP/Electron files** - desktop-only dependencies
4. **Always clear Laravel cache after config changes**
5. **Check Apache logs** if issues persist: `/var/log/apache2/error.log`

## Commands Reference

```bash
# Check Apache config
apache2ctl -S
apache2ctl -t -D DUMP_INCLUDES

# Disable site
sudo a2dissite sitename.conf

# Enable site
sudo a2ensite sitename.conf

# Restart Apache
sudo systemctl restart apache2

# Clear Laravel cache
cd /var/www/html/PROJECT_NAME
php artisan optimize:clear
rm -rf bootstrap/cache/*.php

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```
