#!/bin/bash
cd /var/www/vhosts/tuufi.com/httpdocs
/usr/bin/php artisan generate:tenant-blogs >> storage/logs/blog-cron.log 2>&1
