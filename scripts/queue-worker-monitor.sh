#!/bin/bash

# Queue Worker Monitoring Script
# Bu script queue worker'ın sürekli çalışmasını sağlar

LARAVEL_PATH="/Users/nurullah/Desktop/cms/laravel"
PID_FILE="$LARAVEL_PATH/storage/app/queue-worker.pid"
LOG_FILE="$LARAVEL_PATH/storage/logs/queue-monitor.log"

# Fonksiyonlar
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE"
}

is_worker_running() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p $PID > /dev/null 2>&1; then
            return 0  # Running
        fi
    fi
    return 1  # Not running
}

start_worker() {
    log_message "Starting queue worker..."
    cd "$LARAVEL_PATH"
    
    # Kill any existing workers first
    pkill -f "queue:work" 2>/dev/null
    sleep 2
    
    # Start new worker in background - TÜM QUEUE'LARI DİNLE
    nohup php artisan queue:work --queue=default,module_permissions,translation,high,low --sleep=3 --tries=3 --timeout=120 --memory=512 > "$LARAVEL_PATH/storage/logs/queue-worker.log" 2>&1 &
    
    # Save PID
    echo $! > "$PID_FILE"
    log_message "Queue worker started with PID: $!"
    
    # Update cache
    php artisan tinker --execute="
        Cache::put('queue_worker_pid', '$!', now()->addHours(24));
        Cache::put('queue_worker_status', 'running', now()->addHours(24));
    " > /dev/null 2>&1
}

stop_worker() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        log_message "Stopping queue worker PID: $PID"
        kill -TERM $PID 2>/dev/null
        rm -f "$PID_FILE"
        
        # Update cache
        cd "$LARAVEL_PATH"
        php artisan tinker --execute="
            Cache::forget('queue_worker_pid');
            Cache::forget('queue_worker_status');
        " > /dev/null 2>&1
    fi
}

# Ana mantık
case "${1:-check}" in
    start)
        if is_worker_running; then
            log_message "Worker already running"
        else
            start_worker
        fi
        ;;
    stop)
        stop_worker
        ;;
    restart)
        stop_worker
        sleep 3
        start_worker
        ;;
    check)
        if ! is_worker_running; then
            log_message "Worker not running, starting..."
            start_worker
        else
            log_message "Worker is running"
        fi
        ;;
    status)
        if is_worker_running; then
            PID=$(cat "$PID_FILE")
            echo "Queue worker is running (PID: $PID)"
            log_message "Status check: Worker running (PID: $PID)"
        else
            echo "Queue worker is not running"
            log_message "Status check: Worker not running"
        fi
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|check|status}"
        exit 1
        ;;
esac

exit 0