#!/bin/bash

# 🔍 Laravel 500 Tenant System - Environment Validator
# Validates environment before Docker container startup

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 Laravel 500 Tenant System - Environment Validation${NC}"
echo "========================================================"

# Function to check required environment variable
check_env_var() {
    local var_name=$1
    local var_value=${!var_name}
    
    if [ -z "$var_value" ]; then
        echo -e "${RED}❌ Missing required environment variable: $var_name${NC}"
        return 1
    else
        echo -e "${GREEN}✅ $var_name is set${NC}"
        return 0
    fi
}

# Function to check optional environment variable with default
check_optional_env() {
    local var_name=$1
    local default_value=$2
    local var_value=${!var_name}
    
    if [ -z "$var_value" ]; then
        echo -e "${YELLOW}⚠️  $var_name not set, using default: $default_value${NC}"
        export $var_name="$default_value"
    else
        echo -e "${GREEN}✅ $var_name is set: $var_value${NC}"
    fi
}

# Function to validate database connection
validate_database() {
    echo ""
    echo -e "${BLUE}🗄️  Validating database connection...${NC}"
    
    local max_attempts=30
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; then
            echo -e "${GREEN}✅ Database connection successful${NC}"
            return 0
        else
            echo -e "${YELLOW}⏳ Database not ready (attempt $attempt/$max_attempts)${NC}"
            sleep 2
            ((attempt++))
        fi
    done
    
    echo -e "${RED}❌ Database connection failed after $max_attempts attempts${NC}"
    return 1
}

# Function to validate Redis connection
validate_redis() {
    echo ""
    echo -e "${BLUE}🔴 Validating Redis connection...${NC}"
    
    if redis-cli -h "$REDIS_HOST" -p "$REDIS_PORT" ping > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Redis connection successful${NC}"
        return 0
    else
        echo -e "${RED}❌ Redis connection failed${NC}"
        return 1
    fi
}

# Start validation
echo ""
echo -e "${BLUE}📋 Checking required environment variables...${NC}"

# Critical variables
VALIDATION_FAILED=0

check_env_var "DB_HOST" || VALIDATION_FAILED=1
check_env_var "DB_PORT" || VALIDATION_FAILED=1
check_env_var "DB_DATABASE" || VALIDATION_FAILED=1
check_env_var "DB_USERNAME" || VALIDATION_FAILED=1
check_env_var "DB_PASSWORD" || VALIDATION_FAILED=1
check_env_var "REDIS_HOST" || VALIDATION_FAILED=1
check_env_var "REDIS_PORT" || VALIDATION_FAILED=1
check_env_var "APP_URL" || VALIDATION_FAILED=1

echo ""
echo -e "${BLUE}⚙️  Checking optional variables with defaults...${NC}"

# Optional variables with defaults
check_optional_env "APP_ENV" "production"
check_optional_env "APP_DEBUG" "false"
check_optional_env "LOG_CHANNEL" "stack"
check_optional_env "CACHE_STORE" "redis"
check_optional_env "SESSION_DRIVER" "redis"
check_optional_env "QUEUE_CONNECTION" "redis"

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    echo -e "${YELLOW}⚠️  APP_KEY not set, generating...${NC}"
    export APP_KEY=$(openssl rand -base64 32)
    echo -e "${GREEN}✅ APP_KEY generated${NC}"
fi

# Check if validation failed
if [ $VALIDATION_FAILED -eq 1 ]; then
    echo ""
    echo -e "${RED}❌ Environment validation failed. Please check the missing variables.${NC}"
    exit 1
fi

# Test connections if not in skip mode
if [ "$SKIP_CONNECTION_TESTS" != "true" ]; then
    validate_database || exit 1
    validate_redis || exit 1
fi

echo ""
echo -e "${GREEN}🎉 Environment validation completed successfully!${NC}"
echo -e "${BLUE}🚀 Ready to start Laravel 500 Tenant System${NC}"
echo ""