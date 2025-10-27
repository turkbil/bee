#!/bin/bash

# Database import script for Central Tenant (laravel.test)
# Usage: ./import_database.sh [database_name] [username] [password] [host]

DB_NAME=${1:-"laravel_new"}
DB_USER=${2:-"root"}
DB_PASS=${3:-""}
DB_HOST=${4:-"127.0.0.1"}

echo "=== Laravel Central Tenant Database Import Script ==="
echo "Database: $DB_NAME"
echo "Host: $DB_HOST"
echo "User: $DB_USER"
echo ""

# Create database if not exists
echo "Creating database if not exists..."
mysql -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h "$DB_HOST" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if [ $? -ne 0 ]; then
    echo "❌ Failed to create database!"
    exit 1
fi

echo "✅ Database created/verified successfully"
echo ""

# Function to import table with error handling
import_table() {
    local table_name=$1
    local schema_file="schema/${table_name}.sql"
    local data_file="data/${table_name}.sql"

    echo "📊 Importing table: $table_name"

    # Import schema first
    if [ -f "$schema_file" ]; then
        echo "  ├─ Schema..."
        mysql -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h "$DB_HOST" "$DB_NAME" < "$schema_file"
        if [ $? -eq 0 ]; then
            echo "  ├─ ✅ Schema imported"
        else
            echo "  ├─ ❌ Schema import failed!"
            return 1
        fi
    else
        echo "  ├─ ⚠️  Schema file not found: $schema_file"
    fi

    # Import data
    if [ -f "$data_file" ] && [ -s "$data_file" ]; then
        echo "  ├─ Data..."
        mysql -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h "$DB_HOST" "$DB_NAME" < "$data_file"
        if [ $? -eq 0 ]; then
            echo "  └─ ✅ Data imported"
        else
            echo "  └─ ❌ Data import failed!"
            return 1
        fi
    else
        echo "  └─ ℹ️  No data to import (empty table)"
    fi

    return 0
}

# Import order - dependencies first
ORDERED_TABLES=(
    "migrations"
    "tenants"
    "domains"
    "admin_languages"
    "tenant_languages"
    "roles"
    "permissions"
    "users"
    "model_has_roles"
    "model_has_permissions"
    "role_has_permissions"
    "modules"
    "module_tenants"
    "module_tenant_settings"
    "user_module_permissions"
    "settings_groups"
    "settings"
    "settings_values"
    "themes"
    "menus"
    "menu_items"
    "pages"
    "announcements"
    "portfolio_categories"
    "portfolios"
    "seo_settings"
    "ai_providers"
    "ai_provider_models"
    "ai_credit_packages"
    "ai_credit_purchases"
    "ai_tenant_profiles"
    "ai_profile_sectors"
    "ai_profile_questions"
    "ai_input_groups"
    "ai_feature_categories"
    "ai_features"
    "ai_feature_inputs"
    "ai_feature_prompts"
    "ai_feature_prompt_relations"
    "ai_prompts"
    "widget_categories"
    "widgets"
    "widget_items"
    "widget_modules"
    "tenant_widgets"
    "activity_log"
    "personal_access_tokens"
    "password_reset_tokens"
    "failed_jobs"
    "jobs"
    "job_batches"
    "sessions"
    "cache"
    "cache_locks"
    "media"
    "telescope_entries"
    "telescope_entries_tags"
    "telescope_monitoring"
    "pulse_aggregates"
    "pulse_entries"
    "pulse_values"
    "ai_bulk_operations"
    "ai_content_jobs"
    "ai_context_rules"
    "ai_conversations"
    "ai_messages"
    "ai_credit_transactions"
    "ai_credit_usage"
    "ai_dynamic_data_sources"
    "ai_input_options"
    "ai_model_credit_rates"
    "ai_module_integrations"
    "ai_prompt_cache"
    "ai_prompt_templates"
    "ai_tenant_debug_logs"
    "ai_translation_mappings"
    "ai_usage_analytics"
    "ai_user_preferences"
    "tenant_rate_limits"
    "tenant_resource_limits"
    "tenant_usage_logs"
)

echo "🚀 Starting table import process..."
echo ""

# Import tables in order
FAILED_TABLES=()
SUCCESSFUL_TABLES=()

for table in "${ORDERED_TABLES[@]}"; do
    if import_table "$table"; then
        SUCCESSFUL_TABLES+=("$table")
    else
        FAILED_TABLES+=("$table")
    fi
    echo ""
done

# Fix auto increment values
echo "🔧 Fixing auto increment values..."
if [ -f "reset_auto_increment.sql" ]; then
    mysql -u "$DB_USER" ${DB_PASS:+-p"$DB_PASS"} -h "$DB_HOST" "$DB_NAME" < "reset_auto_increment.sql"
    if [ $? -eq 0 ]; then
        echo "✅ Auto increment values fixed"
    else
        echo "⚠️  Auto increment fix failed, but database import completed"
    fi
else
    echo "ℹ️  No auto increment reset file found"
fi

echo ""
echo "=== Import Summary ==="
echo "✅ Successful tables: ${#SUCCESSFUL_TABLES[@]}"
echo "❌ Failed tables: ${#FAILED_TABLES[@]}"

if [ ${#FAILED_TABLES[@]} -gt 0 ]; then
    echo ""
    echo "Failed tables:"
    for table in "${FAILED_TABLES[@]}"; do
        echo "  - $table"
    done
    echo ""
    echo "⚠️  Import completed with errors. Please check failed tables manually."
    exit 1
else
    echo ""
    echo "🎉 All tables imported successfully!"
    echo ""
    echo "Database '$DB_NAME' is ready to use."
    echo ""
    echo "📝 Next steps:"
    echo "  1. Update your .env file to use database: $DB_NAME"
    echo "  2. Run: php artisan key:generate"
    echo "  3. Run: php artisan migrate:status (to verify)"
    echo "  4. Test your application"
fi