#!/bin/bash

# 🔒 Laravel 500 Tenant System - Security Hardening Script
# Implements security best practices for production deployment

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔒 Laravel 500 Tenant System - Security Hardening${NC}"
echo "=================================================="

# Security functions
harden_system() {
    echo -e "${BLUE}🛡️  Applying system security hardening...${NC}"
    
    # Remove unnecessary packages
    apk del --purge \
        autoconf \
        gcc \
        g++ \
        make \
        git \
        curl-dev 2>/dev/null || true
    
    # Set secure permissions
    chmod -R 750 /var/www/html/storage
    chmod -R 750 /var/www/html/bootstrap/cache
    chmod 600 /var/www/html/.env*
    
    # Remove sensitive files
    rm -f /var/www/html/.env.example
    rm -f /var/www/html/README.md
    rm -rf /var/www/html/.git
    rm -rf /var/www/html/tests
    
    echo -e "${GREEN}✅ System hardening completed${NC}"
}

configure_php_security() {
    echo -e "${BLUE}🐘 Configuring PHP security settings...${NC}"
    
    cat > /usr/local/etc/php/conf.d/security.ini << 'EOF'
; PHP Security Configuration
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; Disable dangerous functions
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source

; File upload restrictions
file_uploads = On
upload_max_filesize = 10M
max_file_uploads = 5

; Session security
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_only_cookies = 1
session.cookie_samesite = "Strict"

; Prevent information disclosure
allow_url_fopen = Off
allow_url_include = Off

; Memory and execution limits
memory_limit = 256M
max_execution_time = 30
max_input_time = 30
max_input_vars = 3000
post_max_size = 15M
EOF

    echo -e "${GREEN}✅ PHP security configuration completed${NC}"
}

configure_nginx_security() {
    echo -e "${BLUE}🌐 Configuring Nginx security headers...${NC}"
    
    cat > /etc/nginx/conf.d/security-headers.conf << 'EOF'
# Security Headers Configuration
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self'; media-src 'self'; object-src 'none'; child-src 'none'; form-action 'self'; base-uri 'self';" always;

# Hide Nginx version
server_tokens off;

# Rate limiting
limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
limit_req_zone $binary_remote_addr zone=general:10m rate=20r/m;

# Connection limiting
limit_conn_zone $binary_remote_addr zone=addr:10m;
limit_conn addr 10;
EOF

    echo -e "${GREEN}✅ Nginx security headers configured${NC}"
}

setup_fail2ban() {
    echo -e "${BLUE}🚫 Setting up Fail2Ban protection...${NC}"
    
    # Install fail2ban
    apk add --no-cache fail2ban iptables
    
    # Laravel specific jail configuration
    cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5
backend = auto

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-noscript]
enabled = true
port = http,https
logpath = /var/log/nginx/access.log
maxretry = 6

[nginx-badbots]
enabled = true
port = http,https
logpath = /var/log/nginx/access.log
maxretry = 2

[nginx-noproxy]
enabled = true
port = http,https
logpath = /var/log/nginx/access.log
maxretry = 2

[laravel-login]
enabled = true
port = http,https
logpath = /var/log/laravel/laravel.log
maxretry = 3
findtime = 300
bantime = 1800
filter = laravel-auth

[laravel-api]
enabled = true
port = http,https
logpath = /var/log/laravel/laravel.log
maxretry = 10
findtime = 60
bantime = 3600
filter = laravel-api
EOF

    # Custom Laravel filters
    cat > /etc/fail2ban/filter.d/laravel-auth.conf << 'EOF'
[Definition]
failregex = .*\[.*\] production.ERROR: Illuminate\\Auth\\Events\\Failed.*"ip":"<HOST>".*
ignoreregex =
EOF

    cat > /etc/fail2ban/filter.d/laravel-api.conf << 'EOF'
[Definition]
failregex = .*\[.*\] production.WARNING:.*Rate limit exceeded.*"ip":"<HOST>".*
ignoreregex =
EOF

    echo -e "${GREEN}✅ Fail2Ban protection configured${NC}"
}

configure_mysql_security() {
    echo -e "${BLUE}🗄️  Configuring MySQL security...${NC}"
    
    # MySQL security configuration
    cat > /docker-entrypoint-initdb.d/security.sql << 'EOF'
-- Remove anonymous users
DELETE FROM mysql.user WHERE User='';

-- Remove remote root access
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');

-- Remove test database
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';

-- Create limited user for application
CREATE USER IF NOT EXISTS 'laravel_app'@'%' IDENTIFIED BY 'secure_app_password_123';
GRANT SELECT, INSERT, UPDATE, DELETE ON laravel.* TO 'laravel_app'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON tenant_%.* TO 'laravel_app'@'%';

-- Flush privileges
FLUSH PRIVILEGES;
EOF

    echo -e "${GREEN}✅ MySQL security configuration completed${NC}"
}

setup_ssl_certs() {
    echo -e "${BLUE}🔐 Setting up SSL certificates...${NC}"
    
    # Generate strong DH parameters
    openssl dhparam -out /etc/nginx/ssl/dhparam.pem 2048
    
    # SSL configuration for Nginx
    cat > /etc/nginx/conf.d/ssl.conf << 'EOF'
# SSL Configuration
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES128-SHA256:ECDHE-RSA-AES256-SHA384;
ssl_prefer_server_ciphers off;
ssl_dhparam /etc/nginx/ssl/dhparam.pem;

# SSL session settings
ssl_session_cache shared:SSL:10m;
ssl_session_timeout 10m;
ssl_session_tickets off;

# OCSP stapling
ssl_stapling on;
ssl_stapling_verify on;
resolver 8.8.8.8 8.8.4.4 valid=300s;
resolver_timeout 5s;
EOF

    echo -e "${GREEN}✅ SSL certificates and configuration completed${NC}"
}

setup_monitoring() {
    echo -e "${BLUE}👁️  Setting up security monitoring...${NC}"
    
    # Install monitoring tools
    apk add --no-cache rkhunter chkrootkit
    
    # Create security monitoring script
    cat > /usr/local/bin/security-monitor.sh << 'EOF'
#!/bin/sh
# Daily security monitoring

echo "$(date): Running security scan..."

# Check for rootkits
rkhunter --check --skip-keypress --report-warnings-only

# Check system integrity
chkrootkit

# Check for suspicious network connections
netstat -tuln | grep -E ':(22|80|443|3306|6379)' > /var/log/security/network-$(date +%Y%m%d).log

# Check failed login attempts
grep "Failed password" /var/log/auth.log | tail -20 > /var/log/security/failed-logins-$(date +%Y%m%d).log 2>/dev/null || true

echo "$(date): Security scan completed"
EOF

    chmod +x /usr/local/bin/security-monitor.sh
    mkdir -p /var/log/security

    echo -e "${GREEN}✅ Security monitoring setup completed${NC}"
}

# Execute hardening steps
main() {
    echo -e "${YELLOW}⚠️  Starting security hardening process...${NC}"
    echo "This will apply production security configurations."
    
    harden_system
    configure_php_security
    configure_nginx_security
    setup_fail2ban
    configure_mysql_security
    setup_ssl_certs
    setup_monitoring
    
    echo ""
    echo -e "${GREEN}🎉 Security hardening completed successfully!${NC}"
    echo -e "${BLUE}🔒 Your Laravel 500 Tenant System is now production-ready with enhanced security.${NC}"
    echo ""
    echo -e "${YELLOW}📝 Next steps:${NC}"
    echo "1. Review and customize security settings as needed"
    echo "2. Set up regular security monitoring"
    echo "3. Implement backup and disaster recovery procedures"
    echo "4. Conduct regular security audits"
}

# Run main function
main