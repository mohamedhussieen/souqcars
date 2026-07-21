# Deployment configs

Reference configs for the Contabo VPS (Ubuntu 22.04, Nginx, PHP 8.3) target. Copy into place
and adjust the domain names before use.

- `nginx-sayarat-api.conf` → `/etc/nginx/sites-available/sayarat-api`
- `nginx-sayarat-dashboard.conf` → `/etc/nginx/sites-available/sayarat-dashboard`
- `supervisor-sayarat-worker.conf` → `/etc/supervisor/conf.d/sayarat-worker.conf`

Enable the nginx sites and reload:

```bash
ln -s /etc/nginx/sites-available/sayarat-api /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/sayarat-dashboard /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

Reload supervisor after adding the worker config:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start sayarat-worker:*
```

Cron (`crontab -e` as the `www-data` user, or a system crontab entry):

```
* * * * * www-data php /var/www/sayarat-api/artisan schedule:run >> /dev/null 2>&1
```

SSL via certbot:

```bash
certbot --nginx -d api.yourdomain.com -d dashboard.yourdomain.com
```

Storage is local disk (`FILESYSTEM_DISK=public` in `.env.production.example`) for this
release — `deploy.sh` runs `php artisan storage:link` so `public/storage` resolves. Cloud
storage (S3/R2) is a planned follow-up and isn't wired up yet.
