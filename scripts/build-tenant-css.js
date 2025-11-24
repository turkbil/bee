#!/usr/bin/env node

/**
 * Tenant CSS Build Script
 * Her tenant icin ayri Tailwind CSS build eder
 *
 * Kullanim:
 *   node scripts/build-tenant-css.js           # Tum tenant'lari build et
 *   node scripts/build-tenant-css.js 2         # Sadece tenant 2 (ixtif)
 *   node scripts/build-tenant-css.js 1001      # Sadece tenant 1001 (muzibu)
 *   node scripts/build-tenant-css.js default   # Sadece default
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

// Tenant listesi
const TENANTS = {
    'default': {
        config: 'tailwind/tenants/default.config.js',
        output: 'public/css/app.css',
        name: 'Default/Central'
    },
    '2': {
        config: 'tailwind/tenants/tenant-2.config.js',
        output: 'public/css/tenant-2.css',
        name: 'ixtif.com'
    },
    '1001': {
        config: 'tailwind/tenants/tenant-1001.config.js',
        output: 'public/css/tenant-1001.css',
        name: 'muzibu.com'
    }
};

// Input CSS dosyasi
const INPUT_CSS = 'resources/css/app.css';

// Renk kodlari
const colors = {
    reset: '\x1b[0m',
    green: '\x1b[32m',
    blue: '\x1b[34m',
    yellow: '\x1b[33m',
    red: '\x1b[31m',
    cyan: '\x1b[36m'
};

function log(color, message) {
    console.log(`${colors[color]}${message}${colors.reset}`);
}

function buildTenant(tenantId, tenant) {
    const startTime = Date.now();

    log('cyan', `\n[${tenant.name}] Building...`);

    // Config dosyasi var mi kontrol et
    if (!fs.existsSync(tenant.config)) {
        log('red', `  ERROR: Config not found: ${tenant.config}`);
        return false;
    }

    try {
        // Tailwind build komutu
        const cmd = `npx tailwindcss -c ${tenant.config} -i ${INPUT_CSS} -o ${tenant.output} --minify`;

        execSync(cmd, { stdio: 'pipe' });

        const duration = Date.now() - startTime;
        const fileSize = fs.statSync(tenant.output).size;
        const fileSizeKB = (fileSize / 1024).toFixed(1);

        log('green', `  SUCCESS: ${tenant.output} (${fileSizeKB} KB) - ${duration}ms`);
        return true;

    } catch (error) {
        log('red', `  ERROR: ${error.message}`);
        return false;
    }
}

function main() {
    const args = process.argv.slice(2);
    const targetTenant = args[0];

    log('blue', '='.repeat(50));
    log('blue', ' Tenant CSS Build System');
    log('blue', '='.repeat(50));

    let tenantsTouild = [];
    let successCount = 0;
    let failCount = 0;

    if (targetTenant) {
        // Belirli tenant build
        if (TENANTS[targetTenant]) {
            tenantsTouild = [[targetTenant, TENANTS[targetTenant]]];
        } else {
            log('red', `Unknown tenant: ${targetTenant}`);
            log('yellow', `Available tenants: ${Object.keys(TENANTS).join(', ')}`);
            process.exit(1);
        }
    } else {
        // Tum tenant'lari build
        tenantsTouild = Object.entries(TENANTS);
    }

    log('yellow', `\nBuilding ${tenantsTouild.length} tenant(s)...`);

    for (const [tenantId, tenant] of tenantsTouild) {
        if (buildTenant(tenantId, tenant)) {
            successCount++;
        } else {
            failCount++;
        }
    }

    log('blue', '\n' + '='.repeat(50));
    log('green', ` Success: ${successCount}`);
    if (failCount > 0) {
        log('red', ` Failed: ${failCount}`);
    }
    log('blue', '='.repeat(50));

    process.exit(failCount > 0 ? 1 : 0);
}

main();
