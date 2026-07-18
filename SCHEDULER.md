# 🕐 Early Warning System - Scheduler Configuration

Dokumentasi lengkap untuk scheduler automation di platform Early Warning System Banjir Rob.

## 📋 Daftar Isi
- [Quick Start](#quick-start)
- [Scheduled Commands](#scheduled-commands)
- [Logging](#logging)
- [Monitoring](#monitoring)
- [Troubleshooting](#troubleshooting)

---

## ⚡ Quick Start

### Setup Cronjob (Production Server)

1. **Edit crontab**
```bash
crontab -e
```

2. **Tambahkan line ini:**
```
* * * * * cd /path/to/early_warning_system && php artisan schedule:run >> /dev/null 2>&1
```

3. **Verify:**
```bash
crontab -l
```

### Local Development (Testing)

```bash
# Run scheduler sekali
php artisan schedule:run

# Run scheduler dalam loop (untuk testing)
while true; do php artisan schedule:run; sleep 60; done
```

---

## 📅 Scheduled Commands

### 1. **Rob Sync** `rob:sync`
- **Interval:** Setiap 1 menit
- **Purpose:** Fetch sensor data dari IoT API Ketapang
- **Logs:** `storage/logs/scheduler.log`
- **Status:** ✅ Active

```bash
# Manual run
php artisan rob:sync
```

---

### 2. **Devices Poll** `devices:poll`
- **Interval:** Setiap 1 menit
- **Purpose:** Poll & store sensor readings ke database
- **Dependencies:** Device API endpoint
- **Status:** ✅ Active

```bash
# Manual run
php artisan devices:poll
```

---

### 3. **Alerts Check** `alerts:check`
- **Interval:** Setiap 1 menit
- **Purpose:** Check kondisi device & trigger alerts
- **Notifications:** Telegram, Email (if configured)
- **Status:** ✅ Active

**Flow:**
1. Evaluate risk level untuk setiap device
2. Compare dengan previous state
3. Trigger notification jika ada perubahan
4. Log hasil ke notification logs

```bash
# Manual run
php artisan alerts:check
```

---

### 4. **Detect Offline Devices** `detect:offline`
- **Interval:** Setiap 1 menit
- **Parameter:** `--minutes=15` (timeout threshold)
- **Purpose:** Mark device sebagai offline jika tidak ada data > 15 menit
- **Status:** ✅ Active

```bash
# Manual run
php artisan command:DetectOfflineDevices --minutes=15
```

---

### 5. **Refresh Predictions** `predictions:refresh`
- **Interval:** Setiap 15 menit
- **Purpose:** Rebuild ML model & generate 2-hour forecast
- **Data Used:** 3 days historical sensor data
- **Horizons:** 15m, 30m, 45m, 60m, 75m, 90m, 105m, 120m
- **Status:** ✅ Active

**Algorithm:**
- Linear Regression
- Features: Water level, slope (60m), wind (X/Y), tide factor
- Training: Last 3 days data
- Output: 8 forecast points

```bash
# Manual run
php artisan predictions:refresh

# Backtest model (untuk verify accuracy)
php artisan predictions:backtest
```

---

### 6. **Refresh BMKG Maritime** `bmkg:maritime`
- **Interval:** Setiap 1 jam
- **Purpose:** Hangatkan cache prakiraan maritim BMKG
- **Note:** Hanya untuk synthetic data (demo/dev)
- **Status:** ✅ Active (Dev), ⚠️ Optional (Prod)

```bash
# Manual run
php artisan bmkg:maritime
```

---

## 📊 Optional Commands (Commented)

### Sensor Cleanup `sensor:cleanup`
```php
Schedule::command('sensor:cleanup')
    ->dailyAt('02:00')  // 2 AM
    ->withoutOverlapping();
```
- **Purpose:** Delete sensor readings older than 7 days
- **Impact:** Optimize database size
- **Safety:** Backup database before enable!

---

### Notifications Archive `notifications:archive`
```php
Schedule::command('notifications:archive')
    ->dailyAt('03:00')  // 3 AM
    ->withoutOverlapping();
```
- **Purpose:** Archive notification logs > 30 days
- **Compliance:** Data retention policy
- **Safety:** Archive ke separate table first

---

### System Health Report `system:health-report`
```php
Schedule::command('system:health-report')
    ->dailyAt('06:00')  // 6 AM
    ->withoutOverlapping();
```
- **Purpose:** Generate daily health report & email to admin
- **Checks:**
  - API connectivity
  - Database health
  - Disk space
  - Memory usage
  - Recent error count

---

## 📝 Logging

### Scheduler Log Location
```
storage/logs/scheduler.log              # Daily rotated (7 days retention)
storage/logs/telegram.log               # Telegram notifications
storage/logs/laravel.log                # General application logs
```

### Log Format
```
[2024-07-18 10:30:45] local.INFO: ✅ rob:sync completed []
[2024-07-18 10:31:15] local.INFO: ✅ alerts:check completed []
[2024-07-18 10:45:00] local.INFO: ✅ predictions:refresh completed []
[2024-07-18 11:00:30] local.ERROR: ❌ bmkg:maritime failed []
```

### View Logs in Real-time
```bash
# Tail scheduler logs
tail -f storage/logs/scheduler.log

# Search for specific command
grep "rob:sync" storage/logs/scheduler.log

# Count daily executions
grep "✅" storage/logs/scheduler.log | wc -l
```

### Configure Log Level
```bash
# .env
LOG_LEVEL=info      # info, debug, warning, error

# Restart scheduler
```

---

## 🔍 Monitoring

### Check Scheduler Status

```bash
# List all scheduled commands
php artisan schedule:list

# Run schedule with verbose output
php artisan schedule:run -v
```

### Verify Cronjob is Running

```bash
# Check if cron daemon is active
systemctl status cron

# Check cronjob logs (depends on system)
grep CRON /var/log/syslog | tail -20
```

### Monitor Execution Time

```bash
# Get stats dari scheduler.log
grep "completed" storage/logs/scheduler.log | head -20
```

### Set Up Email Alerts (Optional)

```php
// routes/console.php
Schedule::command('rob:sync')
    ->everyMinute()
    ->withoutOverlapping()
    ->onFailure(function () {
        Mail::raw('rob:sync failed!', fn($msg) => 
            $msg->to('admin@example.com')->subject('Scheduler Error')
        );
    });
```

---

## ⚙️ Performance Tuning

### Optimize Heavy Commands

**Problem:** Predictions:refresh takes 2+ minutes

**Solution:**
```php
// Cache previous predictions untuk quick comparison
Schedule::command(RefreshPredictions::class)
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();  // Non-blocking
```

### Stagger Commands During Peak Hours

```php
Schedule::command('rob:sync')
    ->everyMinute()
    ->between('06:00', '22:00')      // 6 AM - 10 PM
    ->withoutOverlapping();

Schedule::command('rob:sync')
    ->everyTwoMinutes()
    ->between('22:00', '06:00')      // Night: less frequent
    ->withoutOverlapping();
```

---

## 🐛 Troubleshooting

### Cronjob Not Running

```bash
# 1. Check cron daemon
systemctl status cron

# 2. Verify cronjob entry
crontab -l

# 3. Check system logs
grep CRON /var/log/syslog

# 4. Manual test
cd /path/to/project && php artisan schedule:run
```

### Command Overlapping

```
[ERROR] The "rob:sync" command is already running
```

**Solution:**
- Commands sudah punya `->withoutOverlapping()`
- Check previous execution time vs frequency
- Increase timeout jika command lambat:

```php
Schedule::command('rob:sync')
    ->everyMinute()
    ->withoutOverlapping(timeout: 60);  // Wait 60 sec max
```

### Predictions Not Updated

**Checklist:**
- [ ] Last 10 sensor readings ada di database
- [ ] `predictions:refresh` running setiap 15 menit
- [ ] Check `storage/logs/scheduler.log` untuk error
- [ ] Manual test: `php artisan predictions:refresh`

### Alerts Not Sending

**Checklist:**
- [ ] Telegram bot token configured (.env)
- [ ] User telegram_chat_id set (settings page)
- [ ] `alerts:check` command running
- [ ] Check notification_logs table for status
- [ ] View Telegram logs: `grep telegram storage/logs/scheduler.log`

---

## 🚀 Deployment Checklist

**Before going to production:**

- [ ] Setup cronjob di server
- [ ] Test scheduler runs manually
- [ ] Verify logs in `storage/logs/scheduler.log`
- [ ] Set appropriate `LOG_LEVEL` in .env
- [ ] Configure email alerts untuk failures
- [ ] Setup log rotation (daily, keep 7 days)
- [ ] Monitor disk space untuk logs
- [ ] Set up uptime monitoring (alert jika cron stops)

---

## 📚 References

- [Laravel Scheduler Documentation](https://laravel.com/docs/scheduling)
- [System Crontab Format](https://linux.die.net/man/5/crontab)
- [Monolog Logging](https://seldaek.github.io/monolog/)

---

**Last Updated:** 2024-07-18  
**Maintained By:** EWS Development Team
