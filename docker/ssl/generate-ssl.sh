#!/bin/bash

# 🔐 Laravel 500 Tenant System - SSL Certificate Generator
# Generates self-signed certificates for development and Docker

set -e

echo "🔐 Generating SSL certificates for Laravel 500 Tenant System..."

# Create SSL directory if not exists
mkdir -p /etc/nginx/ssl
cd /etc/nginx/ssl

# Generate private key
echo "📝 Generating private key..."
openssl genrsa -out laravel.test.key 2048

# Generate certificate signing request
echo "📝 Generating certificate signing request..."
openssl req -new -key laravel.test.key -out laravel.test.csr -subj "/C=TR/ST=Istanbul/L=Istanbul/O=Laravel 500 Tenant System/OU=IT Department/CN=laravel.test/emailAddress=admin@laravel.test"

# Generate self-signed certificate
echo "📝 Generating self-signed certificate..."
openssl x509 -req -days 365 -in laravel.test.csr -signkey laravel.test.key -out laravel.test.crt

# Generate combined certificate
echo "📝 Creating combined certificate..."
cat laravel.test.crt laravel.test.key > laravel.test.pem

# Set proper permissions
chmod 600 laravel.test.key
chmod 644 laravel.test.crt laravel.test.pem

echo "✅ SSL certificates generated successfully!"
echo "📄 Files created:"
echo "   - laravel.test.key (Private Key)"
echo "   - laravel.test.crt (Certificate)"
echo "   - laravel.test.pem (Combined)"
echo ""
echo "🔧 To trust this certificate on macOS:"
echo "   sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain laravel.test.crt"