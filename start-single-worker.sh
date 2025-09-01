#!/bin/bash
# Çeviri için tek worker başlat
php artisan queue:work --queue=tenant_isolated --sleep=5 --tries=3 --timeout=300 --once

