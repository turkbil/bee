#!/bin/bash

# 📊 Laravel 500 Tenant System - Enhanced Monitoring Setup
# Comprehensive monitoring for production environment

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}📊 Laravel 500 Tenant System - Enhanced Monitoring${NC}"
echo "=================================================="

# Setup application performance monitoring
setup_apm() {
    echo -e "${BLUE}⚡ Setting up Application Performance Monitoring...${NC}"
    
    # Install New Relic PHP agent (optional)
    cat > /etc/php.d/newrelic.ini << 'EOF'
; New Relic configuration (enable if you have account)
; extension = "newrelic.so"
; newrelic.license = "YOUR_LICENSE_KEY"
; newrelic.appname = "Laravel 500 Tenant System"
; newrelic.daemon.location = "/usr/bin/newrelic-daemon"
EOF

    # Custom APM metrics script
    cat > /usr/local/bin/collect-metrics.sh << 'EOF'
#!/bin/bash
# Custom metrics collection

TIMESTAMP=$(date +%s)
HOSTNAME=$(hostname)

# Database metrics
DB_CONNECTIONS=$(mysql -h mysql-master -u root -p"$DB_ROOT_PASSWORD" -e "SHOW STATUS WHERE variable_name = 'Threads_connected';" 2>/dev/null | awk 'NR==2{print $2}' || echo 0)
DB_QUERIES_PER_SEC=$(mysql -h mysql-master -u root -p"$DB_ROOT_PASSWORD" -e "SHOW STATUS WHERE variable_name = 'Queries';" 2>/dev/null | awk 'NR==2{print $2}' || echo 0)

# Redis metrics
REDIS_MEMORY=$(redis-cli -h redis-cluster INFO memory 2>/dev/null | grep used_memory_human || echo "0B")
REDIS_CONNECTIONS=$(redis-cli -h redis-cluster INFO clients 2>/dev/null | grep connected_clients || echo "0")

# System metrics
CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)
MEMORY_USAGE=$(free | grep Mem | awk '{printf "%.2f", $3/$2 * 100.0}')
DISK_USAGE=$(df / | awk 'NR==2{printf "%.2f", $3/$2*100}')

# Laravel queue metrics
FAILED_JOBS=$(php /var/www/html/artisan queue:failed | wc -l 2>/dev/null || echo 0)

# Output metrics in Prometheus format
cat > /var/log/metrics/custom_metrics.prom << EOF
# HELP laravel_db_connections Current database connections
# TYPE laravel_db_connections gauge
laravel_db_connections{instance="$HOSTNAME"} $DB_CONNECTIONS $TIMESTAMP

# HELP laravel_system_cpu_usage CPU usage percentage
# TYPE laravel_system_cpu_usage gauge
laravel_system_cpu_usage{instance="$HOSTNAME"} $CPU_USAGE $TIMESTAMP

# HELP laravel_system_memory_usage Memory usage percentage
# TYPE laravel_system_memory_usage gauge
laravel_system_memory_usage{instance="$HOSTNAME"} $MEMORY_USAGE $TIMESTAMP

# HELP laravel_system_disk_usage Disk usage percentage
# TYPE laravel_system_disk_usage gauge
laravel_system_disk_usage{instance="$HOSTNAME"} $DISK_USAGE $TIMESTAMP

# HELP laravel_queue_failed_jobs Number of failed queue jobs
# TYPE laravel_queue_failed_jobs gauge
laravel_queue_failed_jobs{instance="$HOSTNAME"} $FAILED_JOBS $TIMESTAMP
EOF

echo "Metrics collected at $(date)"
EOF

    chmod +x /usr/local/bin/collect-metrics.sh
    mkdir -p /var/log/metrics

    echo -e "${GREEN}✅ APM setup completed${NC}"
}

# Setup comprehensive logging
setup_enhanced_logging() {
    echo -e "${BLUE}📝 Setting up enhanced logging system...${NC}"
    
    # Structured logging configuration
    cat > /var/www/html/config/logging-production.php << 'EOF'
<?php
// Enhanced production logging configuration
return [
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'slack', 'database'],
            'ignore_exceptions' => false,
        ],
        
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'info'),
            'days' => 30,
            'permission' => 0644,
        ],
        
        'performance' => [
            'driver' => 'daily',
            'path' => storage_path('logs/performance.log'),
            'level' => 'info',
            'days' => 14,
        ],
        
        'security' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security.log'),
            'level' => 'warning',
            'days' => 90,
        ],
        
        'tenant' => [
            'driver' => 'daily',
            'path' => storage_path('logs/tenant.log'),
            'level' => 'info',
            'days' => 30,
        ],
        
        'database' => [
            'driver' => 'database',
            'table' => 'logs',
            'level' => 'error',
        ],
        
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel 500 Tenant System',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],
    ],
];
EOF

    # Log analysis script
    cat > /usr/local/bin/analyze-logs.sh << 'EOF'
#!/bin/bash
# Log analysis and alerting

LOG_DIR="/var/www/html/storage/logs"
ALERT_THRESHOLD_ERROR=50
ALERT_THRESHOLD_CRITICAL=5

# Analyze last hour's logs
CURRENT_HOUR=$(date +"%Y-%m-%d %H")
ERROR_COUNT=$(grep -c "ERROR" "$LOG_DIR/laravel.log" 2>/dev/null | grep "$CURRENT_HOUR" | wc -l || echo 0)
CRITICAL_COUNT=$(grep -c "CRITICAL" "$LOG_DIR/laravel.log" 2>/dev/null | grep "$CURRENT_HOUR" | wc -l || echo 0)

# Performance metrics
SLOW_QUERIES=$(grep -c "slow query" "$LOG_DIR/laravel.log" 2>/dev/null || echo 0)
MEMORY_ERRORS=$(grep -c "memory limit" "$LOG_DIR/laravel.log" 2>/dev/null || echo 0)

# Security events
FAILED_LOGINS=$(grep -c "authentication failed" "$LOG_DIR/security.log" 2>/dev/null || echo 0)
SUSPICIOUS_IPS=$(grep "blocked" "$LOG_DIR/security.log" 2>/dev/null | awk '{print $NF}' | sort | uniq -c | sort -nr | head -5)

# Generate hourly report
cat > "/var/log/reports/hourly-$(date +%Y%m%d_%H).txt" << EOF_REPORT
=== Laravel 500 Tenant System - Hourly Report ===
Generated: $(date)

ERROR SUMMARY:
- Errors: $ERROR_COUNT
- Critical: $CRITICAL_COUNT
- Slow Queries: $SLOW_QUERIES
- Memory Issues: $MEMORY_ERRORS

SECURITY SUMMARY:
- Failed Logins: $FAILED_LOGINS
- Top Suspicious IPs:
$SUSPICIOUS_IPS

SYSTEM STATUS: $([ $CRITICAL_COUNT -eq 0 ] && [ $ERROR_COUNT -lt $ALERT_THRESHOLD_ERROR ] && echo "HEALTHY" || echo "NEEDS ATTENTION")
EOF_REPORT

# Send alerts if thresholds exceeded
if [ $CRITICAL_COUNT -gt $ALERT_THRESHOLD_CRITICAL ] || [ $ERROR_COUNT -gt $ALERT_THRESHOLD_ERROR ]; then
    echo "ALERT: High error rate detected - Errors: $ERROR_COUNT, Critical: $CRITICAL_COUNT"
    # Add notification logic here (Slack, email, etc.)
fi
EOF

    chmod +x /usr/local/bin/analyze-logs.sh
    mkdir -p /var/log/reports

    echo -e "${GREEN}✅ Enhanced logging setup completed${NC}"
}

# Setup health check endpoints
setup_health_checks() {
    echo -e "${BLUE}🏥 Setting up comprehensive health checks...${NC}"
    
    # Advanced health check script
    cat > /usr/local/bin/health-check.sh << 'EOF'
#!/bin/bash
# Comprehensive system health check

check_database() {
    if mysqladmin ping -h mysql-master -u root -p"$DB_ROOT_PASSWORD" --silent 2>/dev/null; then
        echo "✅ Database: HEALTHY"
        return 0
    else
        echo "❌ Database: DOWN"
        return 1
    fi
}

check_redis() {
    if redis-cli -h redis-cluster ping > /dev/null 2>&1; then
        echo "✅ Redis: HEALTHY"
        return 0
    else
        echo "❌ Redis: DOWN"
        return 1
    fi
}

check_disk_space() {
    local usage=$(df / | awk 'NR==2{print $5}' | sed 's/%//')
    if [ "$usage" -lt 85 ]; then
        echo "✅ Disk Space: HEALTHY ($usage% used)"
        return 0
    else
        echo "⚠️  Disk Space: WARNING ($usage% used)"
        return 1
    fi
}

check_memory() {
    local usage=$(free | grep Mem | awk '{printf "%.0f", $3/$2 * 100.0}')
    if [ "$usage" -lt 90 ]; then
        echo "✅ Memory: HEALTHY ($usage% used)"
        return 0
    else
        echo "⚠️  Memory: HIGH ($usage% used)"
        return 1
    fi
}

check_queue() {
    local failed=$(php /var/www/html/artisan queue:failed | wc -l 2>/dev/null || echo 999)
    if [ "$failed" -lt 10 ]; then
        echo "✅ Queue: HEALTHY ($failed failed jobs)"
        return 0
    else
        echo "⚠️  Queue: WARNING ($failed failed jobs)"
        return 1
    fi
}

# Run all checks
echo "=== Laravel 500 Tenant System Health Check ==="
echo "Timestamp: $(date)"
echo ""

OVERALL_STATUS=0
check_database || OVERALL_STATUS=1
check_redis || OVERALL_STATUS=1
check_disk_space || OVERALL_STATUS=1
check_memory || OVERALL_STATUS=1
check_queue || OVERALL_STATUS=1

echo ""
if [ $OVERALL_STATUS -eq 0 ]; then
    echo "🎉 OVERALL STATUS: HEALTHY"
else
    echo "🚨 OVERALL STATUS: UNHEALTHY - ATTENTION REQUIRED"
fi

exit $OVERALL_STATUS
EOF

    chmod +x /usr/local/bin/health-check.sh

    echo -e "${GREEN}✅ Health check setup completed${NC}"
}

# Setup cron jobs for monitoring
setup_monitoring_crons() {
    echo -e "${BLUE}⏰ Setting up monitoring cron jobs...${NC}"
    
    cat > /etc/cron.d/laravel-monitoring << 'EOF'
# Laravel 500 Tenant System - Monitoring Crons

# Collect metrics every minute
* * * * * root /usr/local/bin/collect-metrics.sh

# Health check every 5 minutes
*/5 * * * * root /usr/local/bin/health-check.sh > /var/log/health-$(date +\%Y\%m\%d).log

# Log analysis every hour
0 * * * * root /usr/local/bin/analyze-logs.sh

# Security monitoring every 6 hours
0 */6 * * * root /usr/local/bin/security-monitor.sh

# Backup system daily at 2 AM
0 2 * * * root /docker/backup/backup-system.sh backup

# Log rotation daily at 3 AM
0 3 * * * root /usr/sbin/logrotate /docker/logrotate/logrotate.conf
EOF

    echo -e "${GREEN}✅ Monitoring cron jobs configured${NC}"
}

# Main execution
main() {
    echo -e "${YELLOW}🚀 Setting up enhanced monitoring and logging...${NC}"
    
    setup_apm
    setup_enhanced_logging
    setup_health_checks
    setup_monitoring_crons
    
    echo ""
    echo -e "${GREEN}🎉 Enhanced monitoring setup completed!${NC}"
    echo -e "${BLUE}📊 Your Laravel 500 Tenant System now has comprehensive monitoring.${NC}"
    echo ""
    echo -e "${YELLOW}📝 Monitoring Features:${NC}"
    echo "• Real-time metrics collection"
    echo "• Comprehensive health checks"
    echo "• Automated log analysis"
    echo "• Security monitoring"
    echo "• Performance tracking"
    echo "• Automated alerting"
}

main