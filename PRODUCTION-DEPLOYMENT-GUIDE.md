# Production Deployment Guide: POS Application
## Caddy + Apache Architecture

---

## 🏗️ Infrastructure Overview

Your production environment uses a **reverse proxy architecture**:

```
Internet (HTTPS Port 443)
         ↓
    Caddy Server
    (HTTPS/SSL Termination)
         ↓
    Routes traffic based on path:
    - /phpmyadmin/* → Apache:8080 (phpMyAdmin)
    - /vpos/hmart/* → Apache:8080 (POS Laravel App) [NEW]
    - /* → Node.js:3000 (Next.js App) [Default]
         ↓
   Apache Server (Port 8080)
   Serves Laravel POS from /var/www/html/VPOS/public
```

**Key Points**:
- ✅ Caddy handles HTTPS (port 443) with automatic SSL certificates
- ✅ Apache runs on port 8080 (HTTP only, internal)
- ✅ Caddy reverse proxies `/vpos/hmart/*` to Apache
- ✅ No direct Apache HTTPS configuration needed

---

## 📋 Pre-Deployment Checklist

- [ ] Server access (SSH credentials)
- [ ] Sudo privileges
- [ ] PHP 8.3+ installed
- [ ] Composer installed
- [ ] MySQL database created: `vpos.hmart`
- [ ] Database user credentials
- [ ] Caddy already running (confirmed ✅)
- [ ] Apache already running on port 8080 (confirmed ✅)
- [ ] Project files ready to upload

---

## 🚀 Deployment Steps

### Step 1: Fix Code Issues (LOCAL - Before Upload)

#### 1.1 Navbar Component Fix
**Already completed** ✅
- Fixed hardcoded `/` to `route('cashier.dashboard')`
- Fixed hardcoded `/images/h_mart.png` to `asset('images/h_mart.png')`

#### 1.2 Build Frontend Assets
```bash
# On local machine
cd e:\Herd\POS
npm install
npm run build
```

This creates `public/build/` directory with compiled assets.

---

### Step 2: Upload Application Files

```bash
# On local machine - create deployment package
tar -czf pos-deployment.tar.gz \
  --exclude=node_modules \
  --exclude=.git \
  --exclude=.env \
  --exclude=storage/logs/* \
  --exclude=storage/framework/cache/* \
  --exclude=storage/framework/sessions/* \
  --exclude=storage/framework/views/* \
  --exclude=bootstrap/cache/*.php \
  --exclude=tests \
  .

# Upload to server
scp pos-deployment.tar.gz user@vertexcoreai.com:/tmp/
```

---

### Step 3: Server Setup

#### 3.1 Extract Files

```bash
# SSH into server
ssh user@vertexcoreai.com

# Create application directory
sudo mkdir -p /var/www/html/VPOS

# Extract files
sudo tar -xzf /tmp/pos-deployment.tar.gz -C /var/www/html/VPOS

# Navigate to directory
cd /var/www/html/VPOS
```

#### 3.2 Environment Configuration

```bash
# Copy production environment template
sudo cp .env.production .env

# Edit with actual credentials
sudo nano .env
```

**Critical .env Settings**:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vertexcoreai.com    # NO /vpos/hmart suffix!
SESSION_PATH=/vpos/hmart             # MUST match URL path
SESSION_DOMAIN=null

DB_HOST=127.0.0.1
DB_DATABASE=vpos.hmart
DB_USERNAME=admin
DB_PASSWORD=YourActualPassword
```

#### 3.3 Set Permissions

```bash
# Set ownership to Apache user
sudo chown -R www-data:www-data /var/www/html/VPOS

# Set directory permissions
sudo find /var/www/html/VPOS -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/html/VPOS -type f -exec chmod 644 {} \;

# Storage and cache need write access
sudo chmod -R 775 /var/www/html/VPOS/storage
sudo chmod -R 775 /var/www/html/VPOS/bootstrap/cache

# Secure .env file
sudo chmod 600 /var/www/html/VPOS/.env
```

#### 3.4 Install Dependencies

```bash
cd /var/www/html/VPOS

# Install Composer dependencies (production mode)
composer install --optimize-autoloader --no-dev --no-interaction

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate --force

# Seed required data (if needed)
php artisan db:seed --class=ChartOfAccountsSeeder --force
php artisan db:seed --class=PayrollSettingsSeeder --force

# Create storage link
php artisan storage:link

# Clear and optimize
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

### Step 4: Configure Apache (Port 8080)

#### 4.1 Deploy Apache Configuration

```bash
# Copy configuration file
sudo cp /var/www/html/VPOS/apache-port-8080.conf \
        /etc/apache2/sites-available/pos-8080.conf

# Enable required modules
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod alias
sudo a2enmod remoteip

# Test configuration
sudo apache2ctl configtest

# Should output: "Syntax OK"
```

#### 4.2 Enable Site

```bash
# Enable POS site configuration
sudo a2ensite pos-8080.conf

# Reload Apache
sudo systemctl reload apache2

# Verify Apache status
sudo systemctl status apache2

# Check Apache is listening on port 8080
sudo netstat -tlnp | grep :8080
```

#### 4.3 Test Apache Directly

```bash
# Test that Apache serves the app on port 8080
curl -I http://127.0.0.1:8080/vpos/hmart

# Should return 302 (redirect to login) or 200
# Should NOT return 404
```

---

### Step 5: Configure Caddy (HTTPS Reverse Proxy)

#### 5.1 Backup Current Caddyfile

```bash
sudo cp /etc/caddy/Caddyfile /etc/caddy/Caddyfile.backup.$(date +%Y%m%d)
```

#### 5.2 Edit Caddyfile

```bash
sudo nano /etc/caddy/Caddyfile
```

**Add this block BEFORE the Next.js reverse_proxy**:

```caddy
vertexcoreai.com {
    # phpMyAdmin (existing)
    reverse_proxy /phpmyadmin* 127.0.0.1:8080

    # POS Laravel Application (NEW - ADD THIS)
    reverse_proxy /vpos/hmart* 127.0.0.1:8080 {
        header_up X-Forwarded-Proto {scheme}
        header_up X-Forwarded-Host {host}
        header_up X-Forwarded-Port {server_port}
        header_up X-Forwarded-For {remote_host}
        header_up X-Real-IP {remote_host}
    }

    # Next.js app (existing - keep as default)
    reverse_proxy 127.0.0.1:3000 {
        header_up X-Forwarded-Proto {scheme}
        header_up X-Forwarded-Host {host}
        header_up X-Forwarded-Port {server_port}
    }
}
```

**CRITICAL**: Order matters! `/vpos/hmart*` must come BEFORE the generic Next.js proxy.

#### 5.3 Validate and Reload Caddy

```bash
# Validate Caddyfile syntax
sudo caddy validate --config /etc/caddy/Caddyfile

# If validation passes, reload Caddy (zero-downtime)
sudo caddy reload --config /etc/caddy/Caddyfile

# Or use systemctl
sudo systemctl reload caddy

# Check Caddy status
sudo systemctl status caddy
```

---

### Step 6: Testing

#### 6.1 Basic Connectivity

```bash
# Test HTTPS access (from server or local machine)
curl -I https://vertexcoreai.com/vpos/hmart

# Should return 302 (redirect to login) if not authenticated
# Or 200 if it's the login page
```

#### 6.2 Browser Testing

1. **Access Application**:
   - URL: `https://vertexcoreai.com/vpos/hmart`
   - Should redirect to: `https://vertexcoreai.com/vpos/hmart/login`

2. **Check Assets**:
   - Open browser DevTools (F12) → Console tab
   - Look for 404 errors on CSS/JS/images
   - Logo should load: `https://vertexcoreai.com/vpos/hmart/images/h_mart.png`

3. **Test Login**:
   - Enter credentials
   - Should redirect to `/vpos/hmart/cashier` (or dashboard)
   - No double prefixes like `/vpos/hmart/vpos/hmart/`

4. **Test Navigation**:
   - Click menu items
   - All URLs should start with `/vpos/hmart/`
   - Session should persist on page refresh

5. **Test HTTPS**:
   - Verify SSL certificate is valid (green padlock)
   - Try accessing via HTTP: `http://vertexcoreai.com/vpos/hmart`
   - Should auto-redirect to HTTPS

#### 6.3 Verify Other Apps Still Work

- **Next.js App**: `https://vertexcoreai.com/` (should still work)
- **phpMyAdmin**: `https://vertexcoreai.com/phpmyadmin` (should still work)

---

### Step 7: Monitor Logs

```bash
# Watch Caddy logs
sudo journalctl -u caddy -f

# Watch Apache error log
sudo tail -f /var/log/apache2/pos-error.log

# Watch Laravel application log
sudo tail -f /var/www/html/VPOS/storage/logs/laravel.log
```

**Look for**:
- 404 errors (routes or assets not found)
- 500 errors (application errors)
- Database connection errors
- Permission denied errors

---

## 🔧 Troubleshooting

### Issue: All Routes Return 404

**Symptoms**: Every URL shows "Not Found"

**Causes & Solutions**:

1. **mod_rewrite not enabled**:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl reload apache2
   ```

2. **AllowOverride not set**:
   - Verify `/etc/apache2/sites-available/pos-8080.conf` has `AllowOverride All`

3. **.htaccess missing**:
   ```bash
   ls -la /var/www/html/VPOS/public/.htaccess
   ```

4. **Caddy not routing correctly**:
   ```bash
   # Test Apache directly
   curl http://127.0.0.1:8080/vpos/hmart/login

   # If this works but HTTPS doesn't, issue is in Caddy config
   ```

---

### Issue: CSS/JS Not Loading

**Symptoms**: Page loads but no styling, browser console shows 404

**Solutions**:

1. **Assets not built**:
   ```bash
   cd /var/www/html/VPOS
   npm run build
   ls -la public/build/assets/
   ```

2. **Permission issues**:
   ```bash
   sudo chmod -R 755 /var/www/html/VPOS/public
   sudo chown -R www-data:www-data /var/www/html/VPOS/public
   ```

3. **Cache issues**:
   ```bash
   php artisan view:clear
   php artisan optimize:clear
   php artisan optimize
   ```

---

### Issue: Login Loops / Session Not Persisting

**Symptoms**: Can't stay logged in, redirects to login after authentication

**Solutions**:

1. **Wrong SESSION_PATH**:
   ```bash
   # In .env, must be:
   SESSION_PATH=/vpos/hmart

   # Then:
   php artisan config:clear
   php artisan config:cache
   ```

2. **Session table missing**:
   ```bash
   mysql -u admin -p vpos.hmart -e "SHOW TABLES LIKE 'sessions';"

   # If missing:
   php artisan migrate --force
   ```

3. **Clear sessions**:
   ```bash
   mysql -u admin -p vpos.hmart -e "TRUNCATE TABLE sessions;"
   ```

---

### Issue: HTTPS Not Working / Mixed Content

**Symptoms**: Site loads on HTTP not HTTPS, or SSL errors

**Solutions**:

1. **Check Caddy status**:
   ```bash
   sudo systemctl status caddy
   sudo journalctl -u caddy -n 50
   ```

2. **Verify Caddyfile**:
   ```bash
   sudo caddy validate --config /etc/caddy/Caddyfile
   ```

3. **Ensure APP_URL is HTTPS**:
   ```bash
   # In .env:
   APP_URL=https://vertexcoreai.com

   # Then:
   php artisan config:cache
   ```

---

### Issue: Logo Image Not Loading

**Symptoms**: Broken image icon where logo should be

**Solutions**:

1. **Verify image exists**:
   ```bash
   ls -la /var/www/html/VPOS/public/images/h_mart.png
   ```

2. **Check permissions**:
   ```bash
   sudo chmod 644 /var/www/html/VPOS/public/images/h_mart.png
   sudo chown www-data:www-data /var/www/html/VPOS/public/images/h_mart.png
   ```

3. **Verify navbar fix was applied**:
   - Should be: `{{ asset('images/h_mart.png') }}`
   - NOT: `/images/h_mart.png`

---

### Issue: Database Connection Failed

**Symptoms**: SQLSTATE errors, can't connect to database

**Solutions**:

1. **Test MySQL connection**:
   ```bash
   mysql -u admin -p -h 127.0.0.1 vpos.hmart
   ```

2. **Verify .env database settings**:
   ```bash
   grep DB_ /var/www/html/VPOS/.env
   ```

3. **Clear config cache**:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

---

### Issue: Permission Denied Errors

**Symptoms**: Can't write to storage, cache errors

**Solutions**:

```bash
# Fix all permissions
sudo chown -R www-data:www-data /var/www/html/VPOS
sudo chmod -R 755 /var/www/html/VPOS
sudo chmod -R 775 /var/www/html/VPOS/storage
sudo chmod -R 775 /var/www/html/VPOS/bootstrap/cache

# Clear Laravel caches
cd /var/www/html/VPOS
php artisan cache:clear
php artisan view:clear
rm -rf bootstrap/cache/*.php
php artisan optimize
```

---

## 🔄 Rollback Procedures

### Quick Rollback - Disable at Caddy Level

```bash
# Edit Caddyfile
sudo nano /etc/caddy/Caddyfile

# Comment out the /vpos/hmart* reverse_proxy block

# Reload Caddy
sudo caddy reload --config /etc/caddy/Caddyfile
```

### Full Rollback - Restore from Backup

```bash
# Stop services
sudo systemctl stop apache2

# Restore application
sudo rm -rf /var/www/html/VPOS
sudo tar -xzf /backups/pos-backup-YYYYMMDD.tar.gz -C /var/www/html/

# Restore database
mysql -u admin -p vpos.hmart < /backups/pos-db-YYYYMMDD.sql

# Restore Caddy config
sudo cp /etc/caddy/Caddyfile.backup /etc/caddy/Caddyfile
sudo caddy reload --config /etc/caddy/Caddyfile

# Start Apache
sudo systemctl start apache2
```

---

## 📊 Post-Deployment Checklist

### Application Access
- [ ] `https://vertexcoreai.com/vpos/hmart` loads
- [ ] Redirects to `/vpos/hmart/login`
- [ ] Login page displays correctly
- [ ] All CSS/JS/images load (no 404s in console)
- [ ] Logo image visible

### Authentication
- [ ] Can login with valid credentials
- [ ] Redirects to dashboard after login
- [ ] Session persists on page refresh
- [ ] Logout works correctly

### Navigation
- [ ] All menu links work
- [ ] URLs correct (`/vpos/hmart/...`)
- [ ] No double prefixes
- [ ] No 404 errors

### HTTPS/SSL
- [ ] HTTPS works (green padlock)
- [ ] HTTP auto-redirects to HTTPS
- [ ] Valid SSL certificate
- [ ] No mixed content warnings

### Functionality
- [ ] POS interface works
- [ ] Product management works
- [ ] Sales can be created
- [ ] Reports load correctly
- [ ] Database queries succeed

### Other Apps
- [ ] Next.js app still works: `https://vertexcoreai.com/`
- [ ] phpMyAdmin still works: `https://vertexcoreai.com/phpmyadmin`

### Logs
- [ ] No errors in Caddy logs
- [ ] No errors in Apache logs
- [ ] No errors in Laravel logs
- [ ] Performance acceptable (<1s response time)

---

## 🎯 Architecture Summary

**Request Flow**:
```
User Browser
    ↓
https://vertexcoreai.com/vpos/hmart/products
    ↓
Caddy (Port 443)
- Terminates SSL
- Matches path /vpos/hmart*
- Adds X-Forwarded-* headers
    ↓
Reverse Proxy to Apache
http://127.0.0.1:8080/vpos/hmart/products
    ↓
Apache (Port 8080)
- Receives request with full path
- Alias: /vpos/hmart → /var/www/html/VPOS/public
- Serves: /var/www/html/VPOS/public/products
    ↓
.htaccess (in public/)
- No file exists at /products
- Rewrite to index.php
    ↓
Laravel
- Receives clean path: /products
- route('products.index') generates: /products
- Browser resolves as: /vpos/hmart/products ✓
```

**Why This Works**:
- Caddy handles HTTPS (SSL certificates, port 443)
- Apache serves Laravel app (internal, port 8080)
- Reverse proxy is transparent to Laravel
- X-Forwarded headers tell Laravel about HTTPS
- `APP_URL` is base domain (Caddy handles subdirectory routing)
- `SESSION_PATH=/vpos/hmart` isolates sessions from other apps

---

## 📝 Key Configuration Files

1. **`.env.production`** (template, copy to `.env` on server)
   - `APP_URL=https://vertexcoreai.com`
   - `SESSION_PATH=/vpos/hmart`

2. **`Caddyfile-update`** (merge into `/etc/caddy/Caddyfile`)
   - Reverse proxy `/vpos/hmart*` to Apache

3. **`apache-port-8080.conf`** (deploy to `/etc/apache2/sites-available/`)
   - Alias `/vpos/hmart` to app public directory

4. **`resources/views/components/navbar.blade.php`** (already fixed)
   - Uses `route()` and `asset()` helpers

5. **`public/.htaccess`** (standard Laravel, no changes needed)
   - No `RewriteBase` directive

---

## 🚦 Success Criteria

Deployment is **SUCCESSFUL** when:

✅ App accessible at `https://vertexcoreai.com/vpos/hmart`
✅ HTTPS working with valid SSL
✅ Login functionality works
✅ All assets load (CSS, JS, images)
✅ Navigation works (no 404s, no double prefixes)
✅ Sessions persist correctly
✅ No errors in logs
✅ Other apps (Next.js, phpMyAdmin) still work
✅ Performance acceptable (<1s response time)

---

## 📞 Support Resources

- **Caddy Documentation**: https://caddyserver.com/docs/
- **Apache mod_proxy**: https://httpd.apache.org/docs/2.4/mod/mod_proxy.html
- **Laravel Deployment**: https://laravel.com/docs/12.x/deployment
- **Your Project Docs**: `APACHE_SUBDIRECTORY_SETUP.md`

---

**Deployment Guide Version**: 2.0 (Caddy + Apache Architecture)
**Created**: 2025-11-25
**Target URL**: https://vertexcoreai.com/vpos/hmart
**Approach**: Caddy Reverse Proxy + Apache Backend
**Estimated Time**: 1-2 hours
