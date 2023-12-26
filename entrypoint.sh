#!/bin/bash
set -e

# Install the cron job
crontab /etc/cron.d/my-cronjob

# Start the cron daemon in the background
cron &

# Tail the log file in the background
tail -f /var/www/html/test_cron.log &

# Start Apache using exec
exec apache2-foreground