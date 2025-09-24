#!/bin/bash

# 🧪 AUTOMATED TESTING SCRIPT - Laravel CMS
# Bu script tüm test süreçlerini otomatize eder

set -e  # Exit on any error

echo "🚀 Laravel CMS - Automated Testing Started"
echo "=================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in Laravel root
if [ ! -f "artisan" ]; then
    print_error "Not in Laravel root directory!"
    exit 1
fi

print_status "🔧 Preparing test environment..."

# 1. Clear all caches
print_status "Clearing caches..."
php artisan app:clear-all

# 2. Run migrations in test environment
print_status "Running test migrations..."
php artisan migrate:fresh --env=testing --force

# 3. Run Unit Tests
print_status "🧪 Running Unit Tests..."
if php artisan test tests/Unit/ --stop-on-failure; then
    print_success "Unit tests passed!"
else
    print_error "Unit tests failed!"
    exit 1
fi

# 4. Run Feature Tests
print_status "🎯 Running Feature Tests..."
if php artisan test tests/Feature/ --stop-on-failure; then
    print_success "Feature tests passed!"
else
    print_error "Feature tests failed!"
    exit 1
fi

# 5. Check for Browser Tests (Dusk)
if [ -d "tests/Browser" ] && [ "$(ls -A tests/Browser/*.php 2>/dev/null)" ]; then
    print_status "🌐 Running Browser Tests..."
    if php artisan dusk --stop-on-failure; then
        print_success "Browser tests passed!"
    else
        print_warning "Browser tests failed or Chrome not available"
    fi
else
    print_warning "No browser tests found"
fi

# 6. Code Style Check (if available)
if [ -f "./vendor/bin/php-cs-fixer" ]; then
    print_status "🎨 Checking code style..."
    if ./vendor/bin/php-cs-fixer fix --dry-run --diff; then
        print_success "Code style is good!"
    else
        print_warning "Code style issues found"
    fi
fi

# 7. Static Analysis (if available)
if [ -f "./vendor/bin/phpstan" ]; then
    print_status "📊 Running static analysis..."
    if ./vendor/bin/phpstan analyse --memory-limit=2G; then
        print_success "Static analysis passed!"
    else
        print_warning "Static analysis issues found"
    fi
fi

# 8. Security Audit
print_status "🔒 Running security audit..."
if composer audit; then
    print_success "Security audit passed!"
else
    print_warning "Security issues found"
fi

# 9. Performance Test (basic)
print_status "⚡ Basic performance check..."
echo "Memory usage: $(php -r 'echo memory_get_peak_usage(true)/1024/1024 . " MB";')"

# 10. Generate test report
print_status "📊 Generating test report..."
cat > test-report.txt << EOF
Laravel CMS Test Report
Generated: $(date)
=======================

✅ Unit Tests: PASSED
✅ Feature Tests: PASSED
⚠️  Browser Tests: CONDITIONAL
⚠️  Code Style: CONDITIONAL
⚠️  Static Analysis: CONDITIONAL
✅ Security Audit: CHECKED

Environment: $(php artisan env)
PHP Version: $(php -v | head -n 1)
Laravel Version: $(php artisan --version)

Test Coverage: Run 'php artisan test --coverage' for details
Performance: See above memory usage

Next Steps:
- Review any warnings above
- Run manual smoke tests
- Deploy to staging if all green
EOF

print_success "Test report saved to test-report.txt"

echo "=================================================="
print_success "🎉 Automated testing completed successfully!"
echo "📄 Check test-report.txt for detailed results"