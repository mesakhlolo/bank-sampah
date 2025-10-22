# Deployment Checklist - Bank Sampah MVP

## ✅ Pre-Deployment Checklist

### Environment Configuration

-   [ ] `.env.production` sudah dikonfigurasi dengan benar
-   [ ] `APP_ENV=production`
-   [ ] `APP_DEBUG=false`
-   [ ] `APP_KEY` sudah di-generate
-   [ ] Database credentials production sudah benar
-   [ ] Mail configuration sudah benar (jika ada)

### Code Quality

-   [x] Semua tests passing
-   [x] Laravel Pint formatting sudah dijalankan
-   [x] No security vulnerabilities
-   [x] No deprecated code

### Optimization

-   [ ] Run production optimization commands:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### Database

-   [ ] Backup database production
-   [ ] Run migrations di production:

```bash
php artisan migrate --force
```

-   [ ] Run seeders jika diperlukan (fresh installation):

```bash
php artisan db:seed --force
```

### File Permissions

-   [ ] Set proper permissions:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Security

-   [ ] HTTPS enabled
-   [ ] CSRF protection enabled
-   [ ] Session secure cookies (SESSION_SECURE_COOKIE=true)
-   [ ] Rate limiting configured
-   [ ] Password policy enforced

### Monitoring

-   [ ] Error logging configured
-   [ ] Performance monitoring enabled
-   [ ] Backup strategy in place
-   [ ] Queue workers running (if applicable)

---

## 🚀 Deployment Steps

### 1. Clone Repository

```bash
git clone <repository-url> bank-sampah
cd bank-sampah
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3. Configure Environment

```bash
cp .env.example .env
nano .env  # Edit configuration
php artisan key:generate
```

### 4. Database Setup

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Configure Web Server

**Nginx Example:**

```nginx
server {
    listen 80;
    server_name bank-sampah.example.com;
    root /var/www/bank-sampah/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. SSL/TLS Configuration

```bash
certbot --nginx -d bank-sampah.example.com
```

### 9. Queue Workers (Optional)

```bash
# Create supervisor config
sudo nano /etc/supervisor/conf.d/bank-sampah-worker.conf
```

```ini
[program:bank-sampah-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bank-sampah/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/bank-sampah/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bank-sampah-worker:*
```

### 10. Scheduler (Optional)

Add to crontab:

```bash
* * * * * cd /var/www/bank-sampah && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Post-Deployment Testing

### Functional Tests

-   [ ] Login dengan user admin
-   [ ] Login dengan user operator
-   [ ] Create new customer
-   [ ] Create deposit transaction
-   [ ] Create withdrawal transaction
-   [ ] View reports
-   [ ] Download PDF receipts
-   [ ] Check responsive design di mobile

### Performance Tests

-   [ ] Page load time < 3 seconds
-   [ ] Database query optimization
-   [ ] No N+1 queries
-   [ ] Assets loading properly

### Security Tests

-   [ ] SQL injection protection
-   [ ] XSS protection
-   [ ] CSRF protection
-   [ ] Session security
-   [ ] File upload security

---

## 📊 Monitoring & Maintenance

### Daily

-   [ ] Check error logs
-   [ ] Monitor disk space
-   [ ] Check database size

### Weekly

-   [ ] Database backup
-   [ ] Review user activity
-   [ ] Check performance metrics

### Monthly

-   [ ] Security updates
-   [ ] Dependency updates
-   [ ] Performance optimization

---

## 🆘 Troubleshooting

### Common Issues

**Issue: 500 Internal Server Error**

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

**Issue: Permission Denied**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Issue: Database Connection Error**

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

**Issue: Assets Not Loading**

```bash
# Rebuild assets
npm run build

# Check symbolic link
php artisan storage:link
```

---

## 📝 Rollback Plan

Jika deployment gagal:

1. Restore database backup:

```bash
mysql -u root -p bank_sampah < backup.sql
```

2. Revert to previous version:

```bash
git checkout <previous-commit-hash>
composer install --no-dev
php artisan migrate:rollback
```

3. Clear all caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

**Deployment Date:** ******\_******
**Deployed By:** ******\_******
**Version:** ******\_******
