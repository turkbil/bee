<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel whereSlug(string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel withoutTrashed()
 */
	class BaseModel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tenant_id
 * @property string $domain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tenant $tenant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain whereUpdatedAt($value)
 */
	class Domain extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $module_name
 * @property array<array-key, mixed> $settings
 * @property array<array-key, mixed>|null $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting whereModuleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModuleTenantSetting whereUpdatedAt($value)
 */
	class ModuleTenantSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $seoable_type
 * @property int $seoable_id
 * @property array<array-key, mixed>|null $titles
 * @property array<array-key, mixed>|null $descriptions
 * @property string|null $keywords
 * @property string|null $canonical_url
 * @property string|null $author
 * @property string|null $author_names
 * @property string|null $author_urls
 * @property array<array-key, mixed>|null $og_titles
 * @property array<array-key, mixed>|null $og_descriptions
 * @property string|null $og_image
 * @property string|null $og_images
 * @property string $og_type
 * @property string $twitter_card
 * @property string|null $twitter_title
 * @property string|null $twitter_description
 * @property string|null $twitter_image
 * @property array<array-key, mixed>|null $robots_meta
 * @property string|null $focus_keywords
 * @property string|null $additional_keywords
 * @property int $seo_score
 * @property array<array-key, mixed>|null $seo_analysis
 * @property \Illuminate\Support\Carbon|null $last_analyzed
 * @property int $content_length
 * @property int $keyword_density
 * @property array<array-key, mixed>|null $readability_score
 * @property array<array-key, mixed>|null $page_speed_insights
 * @property \Illuminate\Support\Carbon|null $last_crawled
 * @property array<array-key, mixed>|null $analysis_results
 * @property string|null $analysis_date
 * @property string|null $strengths
 * @property string|null $improvements
 * @property string|null $action_items
 * @property array<array-key, mixed>|null $ai_suggestions
 * @property string $status
 * @property int $priority_score
 * @property string|null $priority_scores
 * @property string|null $schema_types Schema.org page types per language for Google Rich Results
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $seoable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting byScore(string $operator = '>=', int $score = 80)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting highPriority()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting needsAnalysis()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereActionItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereAdditionalKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereAiSuggestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereAnalysisDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereAnalysisResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereAuthorNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereAuthorUrls($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereCanonicalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereContentLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereDescriptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereFocusKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereImprovements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereKeywordDensity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereLastAnalyzed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereLastCrawled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereOgDescriptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereOgImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereOgImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereOgTitles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereOgType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting wherePageSpeedInsights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting wherePriorityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting wherePriorityScores($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereReadabilityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereRobotsMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereSchemaTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereSeoAnalysis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereSeoScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereSeoableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereSeoableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereStrengths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereTitles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereTwitterCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereTwitterDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereTwitterImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SeoSetting whereTwitterTitle($value)
 */
	class SeoSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $tenancy_db_name
 * @property bool $is_active
 * @property bool $central
 * @property string|null $fullname
 * @property string|null $email
 * @property string|null $phone
 * @property int $theme_id
 * @property string $admin_default_locale
 * @property string $tenant_default_locale
 * @property array<array-key, mixed>|null $data
 * @property float $ai_credits_balance
 * @property \Illuminate\Support\Carbon|null $ai_last_used_at
 * @property int|null $tenant_ai_provider_id
 * @property int|null $tenant_ai_provider_model_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Modules\AI\App\Models\AIProvider|null $aiProvider
 * @property-read \Modules\AI\App\Models\AIProviderModel|null $aiProviderModel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Stancl\Tenancy\Database\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\ModuleManagement\App\Models\Module> $modules
 * @property-read int|null $modules_count
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereAdminDefaultLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereAiCreditsBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereAiLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCentral($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTenancyDbName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTenantAiProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTenantAiProviderModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTenantDefaultLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereThemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 */
	class Tenant extends \Eloquent implements \Stancl\Tenancy\Contracts\TenantWithDatabase {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $bio
 * @property bool $is_active
 * @property string|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $admin_locale
 * @property string|null $tenant_locale
 * @property array<array-key, mixed>|null $dashboard_preferences
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\UserManagement\App\Models\UserModulePermission> $userModulePermissions
 * @property-read int|null $user_module_permissions_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAdminLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDashboardPreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTenantLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

