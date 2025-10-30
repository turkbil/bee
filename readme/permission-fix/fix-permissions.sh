#!/bin/bash

# Laravel Permission & Cache Auto-Fix Script
# Usage: bash readme/permission-fix/fix-permissions.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$(dirname "$SCRIPT_DIR")")"

echo "🛠️  Laravel Permission & Cache Fix"
echo "=================================="
echo ""

cd "$PROJECT_ROOT"

# STEP 1: Fix Permissions
echo "📁 Step 1/4: Fixing permissions..."
find storage -type d -exec chmod 775 {} \; 2>/dev/null || true
find storage -type f -exec chmod 664 {} \; 2>/dev/null || true
find bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
find bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true

chown -R tuufi.com_:psacln storage bootstrap/cache 2>/dev/null || true

echo "✅ Permissions fixed"

# STEP 2: Config Cache Refresh
echo ""
echo "⚙️  Step 2/4: Refreshing config cache..."
composer config-refresh --no-interaction

echo "✅ Config cache refreshed"

# STEP 3: OPcache Reset
echo ""
echo "🔄 Step 3/4: Resetting OPcache..."
curl -s -k https://ixtif.com/opcache-reset.php > /dev/null 2>&1 || echo "⚠️  OPcache reset skipped (optional)"

# STEP 4: Verify
echo ""
echo "🧪 Step 4/4: Verifying..."

# Check config cache
if [ -f "bootstrap/cache/config.php" ]; then
    echo "✅ Config cache exists"
else
    echo "❌ Config cache missing!"
    exit 1
fi

# Check permissions
if [ -w "storage/framework/views" ]; then
    echo "✅ Storage writable"
else
    echo "❌ Storage not writable!"
    exit 1
fi

echo ""
echo "=================================="
echo "🎉 All fixed successfully!"
echo "=================================="
echo ""
echo "Next steps:"
echo "  1. Test your application: https://ixtif.com"
echo "  2. Check logs: tail -f storage/logs/laravel.log"
echo ""
