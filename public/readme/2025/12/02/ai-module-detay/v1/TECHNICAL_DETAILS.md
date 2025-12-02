# Modules/AI - Teknik Detaylar & API Dokümantasyonu

## 1. Directory Structure

```
Modules/AI/
├── app/
│   ├── Console/
│   │   ├── CacheWarmupCommand.php
│   │   └── Commands/
│   ├── Contracts/
│   │   ├── AIContentGeneratable.php (Interface)
│   │   ├── ModuleSearchInterface.php
│   │   └── TenantPromptServiceInterface.php
│   ├── Events/ (11 event classes)
│   ├── Exceptions/ (10 exception classes)
│   ├── Helpers/
│   │   └── UniversalInputHelpers.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/ (40+ controllers)
│   │   │   └── Api/
│   │   ├── Livewire/ (20+ components)
│   ├── Jobs/ (15+ queue jobs)
│   ├── Models/ (25 models)
│   ├── Repositories/
│   ├── Services/ (240+ services!)
│   ├── Traits/
│   │   ├── HasAIContentGeneration.php
│   │   └── HasModelBasedCredits.php
│   └── Livewire/ (Knowledge Base management)
├── config/
│   ├── config.php (DeepSeek, OpenAI config)
│   └── universal-input-system.php (24.4 KB!)
├── database/
│   ├── migrations/ (35+ migrations)
│   │   └── tenant/ (tenant-specific migrations)
│   └── seeders/ (15+ seeders)
├── resources/
│   └── views/ (Blade templates)
├── routes/
│   ├── web.php
│   ├── admin.php (69 KB!)
│   └── api.php
└── tests/
```

## 2. Service Architecture (240+ Services!)

### Core Services
- **AIService** - Main AI service (provider selection, streaming, token management)
- **AIImageGenerationService** - DALL-E 3 integration
- **AdvancedSeoIntegrationService** - SEO analysis & optimization
- **AITranslationService** - Multi-language translation

### Workflow Services
- **FlowExecutor** - Main workflow execution engine
- **NodeExecutor** - Single node execution
- **ParallelNodeExecutor** - Parallel node execution
- **NodeFactory** - Node creation

### Assistant Services
- **AssistantTypeResolver** - Intent detection & module selection
- **ShopSearchService** - E-commerce product search
- **MusicSearchService** - Music/song search (Muzibu)
- **ContentSearchService** - Blog/content search
- **BookingSearchService** - Booking/reservation search

### Content Generation
- **AIContentGeneratorService** - GLOBAL content generation
- **PromptGenerator** - Prompt template generation
- **PromptEnhancer** - Prompt optimization
- **SmartProfileBuilder** - Profile-based context

### Translation Services
- **FastHtmlTranslationService** - Optimized HTML translation
- **UltraAssertiveTranslationPrompt** - Zero-refusal translation
- **StreamingTranslationEngine** - Real-time translation
- **EnhancedChunkTranslationJob** - Batch translation

### Notification Services
- **TelegramNotificationService** - Telegram Bot API integration
- **PhoneNumberDetectionService** - Phone number detection & formatting

### Context Services
- **ContextEngine** - Context building
- **TenantContextCollector** - Tenant-specific context
- **UserContextCollector** - User profile context
- **PageContextCollector** - Page-specific context

### Credit & Monitoring
- **AICreditService** - Credit balance management
- **ModelBasedCreditService** - Model-specific pricing
- **GlobalAIMonitoringService** - System monitoring
- **MonitoringService** - Performance monitoring

### Caching & Optimization
- **TenantAwareCacheService** - Tenant-safe caching
- **ProviderOptimizationService** - Provider selection optimization
- **QueueOptimizationService** - Queue job optimization

## 3. Key Models (25 Total)

### Conversation Models
```php
AIConversation - Chat conversations
AIMessage - Individual messages
Conversation - Redis/cache-based (legacy)
Message - Legacy message model
```

### Feature Models
```php
AIFeature - Feature definitions
AIFeatureCategory - Feature grouping
AIFeatureInput - Form input definitions
AIFeaturePrompt - Feature-specific prompts
AIFeaturePromptRelation - Many-to-many relationships
AIInputGroup - Input grouping
AIInputOption - Dropdown options
```

### Configuration Models
```php
AIProvider - Provider definitions (DeepSeek, OpenAI, etc.)
AIProviderModel - Available models per provider
AITenantProfile - Tenant AI profile & settings
AIProfileQuestion - Profile questionnaire
AIProfileSector - Tenant sector information
AIDynamicDataSource - Dynamic data for forms
```

### Credit & Usage Models
```php
AICreditPackage - Credit packages for sale
AICreditPurchase - Purchase history
AICreditUsage - Detailed usage tracking
AICreditTransaction - Transaction history
AIModelCreditRate - Model-specific pricing
```

### Workflow & Advanced Models
```php
Flow - Workflow/flow definitions
Prompt - Prompt library
AIContextRules - Context engine rules
AIBulkOperation - Bulk operation tracking
AITranslationMapping - Translation mappings
AIUsageAnalytics - Analytics data
AIPromptCache - Cached prompts
AITenantDebugLog - Debug logging
```

## 4. Controller Structure

### Admin Controllers
- **AIChatController** - Chat management
- **AIFeaturesController** - Feature CRUD
- **AIImageController** - Image generation
- **TranslationController** - Translation interface
- **AnalyticsController** - Usage analytics
- **BulkOperationController** - Bulk operations
- **AIProfileController** - Profile setup
- **SeoAnalysisController** - SEO analysis dashboard
- **SettingsController** - Provider settings
- **TokenController** - Token management
- **TemplateController** - Template management
- **MonitoringController** - System monitoring

### Livewire Components
- **FlowEditor** - Visual workflow editor
- **ChatPanel** - Chat UI
- **AIFeaturesDashboard** - Feature management
- **AIProfileManagement** - Profile setup wizard
- **UniversalInputComponent** - Dynamic form renderer
- **TranslationPanel** - Translation UI

### API Controllers
- **PublicAIController** - Public API endpoints

## 5. Workflow Nodes (12 Types)

```
BaseNode (abstract)
├── AIResponseNode - Generates AI responses
├── WelcomeNode - Welcome message
├── MessageSaverNode - Saves conversation
├── CategoryDetectionNode - Intent detection
├── ProductSearchNode - Product search
├── StockSorterNode - Inventory sorting
├── MeilisearchSettingsNode - Search config
├── ContextBuilderNode - Context building
├── EndNode - Workflow termination
└── Conditional nodes (implied)
```

## 6. Configuration Deep Dive

### universal-input-system.php (24.4 KB)

```php
// Performance Settings
'performance' => [
    'max_concurrent_submissions' => 10,
    'request_timeout' => 30,
    'memory_limit' => 256, // MB
    'max_input_size' => 1048576, // 1MB
    'monitoring_enabled' => true,
]

// Cache Configuration
'cache' => [
    'ttl' => [
        'form_structure' => 3600,      // 1 hour
        'smart_defaults' => 1800,      // 30 min
        'dynamic_options' => 900,      // 15 min
        'analytics' => 300,            // 5 min
        'context_data' => 600,         // 10 min
        'validation_rules' => 7200,    // 2 hours
    ],
    'auto_clear' => true,
    'warming' => [
        'enabled' => false,
        'schedule' => '0 */6 * * *',
    ],
]

// AI Provider Config
'ai' => [
    'default_provider' => 'openai',
    'providers' => [
        'openai' => [
            'model' => 'gpt-4',
            'max_tokens' => 4000,
            'rate_limit' => [
                'requests_per_minute' => 60,
                'tokens_per_minute' => 100000,
            ],
        ],
        'anthropic' => [
            'model' => 'claude-3-sonnet',
            'max_tokens' => 4000,
        ],
    ],
]
```

## 7. Credit System Details

### Cost Breakdown
```
Image Generation:
- HD (1024x1024): 1 credit
- Standard: 0.5 credit

Content Generation:
- Simple: 3 credits
- Moderate: 5 credits  
- Complex: 10 credits
- Template-based: 2 credits
- PDF enhanced: 8 credits (OPTIMIZED from 15)

Translation:
- Per 1000 chars: 1 credit
```

### Usage Tracking
```php
AICreditUsage record structure:
{
    'tenant_id' => int,
    'user_id' => int,
    'usage_type' => 'image_generation|content_generation|translation',
    'provider_name' => 'openai|deepseek|anthropic',
    'model' => 'dall-e-3|gpt-4|etc',
    'credits_deducted' => float,
    'metadata' => [
        'prompt' => string,
        'operation_type' => 'manual|blog_auto|product_auto',
        'media_id' => string,
        'quality' => 'hd|standard',
    ]
}
```

## 8. Database Migrations Summary

### Central Database Tables (tuufi_4ekim)
- ai_providers
- ai_provider_models
- ai_credit_packages
- ai_credit_purchases
- ai_credit_usages
- ai_credit_transactions
- ai_model_credit_rates
- ai_conversations (central sync)

### Tenant Database Tables
- ai_features
- ai_feature_categories
- ai_feature_inputs
- ai_feature_prompts
- ai_feature_prompt_relations
- ai_input_groups
- ai_input_options
- ai_prompts
- ai_messages
- ai_flows (workflows)
- ai_tenant_profiles
- ai_profile_questions
- ai_profile_sectors
- ai_dynamic_data_sources
- ai_module_integrations
- ai_context_rules
- ai_bulk_operations
- ai_translation_mappings
- ai_usage_analytics
- ai_prompt_cache
- ai_tenant_debug_logs

## 9. Route Structure

### Web Routes
```
/ai/chat - Chat interface
/ai/features - Feature list
/ai/settings - Settings
/ai/analytics - Analytics dashboard
/ai/billing - Credit management
```

### Admin Routes (69 KB admin.php!)
```
/admin/ai/chat - Chat management
/admin/ai/features - Feature CRUD
/admin/ai/images - Image generation
/admin/ai/translations - Translation interface
/admin/ai/analytics - Usage analytics
/admin/ai/bulk - Bulk operations
/admin/ai/profile - Profile wizard
/admin/ai/seo - SEO analysis
/admin/ai/workflow - Workflow editor
/admin/ai/settings - Provider config
/admin/ai/monitoring - System monitoring
```

### API Routes
```
/api/ai/chat - Chat API
/api/ai/content - Content generation
/api/ai/translate - Translation API
/api/ai/images - Image generation API
```

## 10. Integration Points

### Tenant-Specific Features
```php
// Tenant 2 (ixtif.com) - Forklift/Transpalet
Tenant2ProductSearchService - Custom product search
Tenant2PromptService - Custom prompts

// Tenant 1001 (muzibu.com.tr) - Music
MusicSearchService - Song/album/artist search
```

### Module Integration
```php
// Blog Module
AIContentGeneratorService -> Blog content
AIImageGenerationService -> Blog cover image
AdvancedSeoIntegrationService -> SEO optimization

// Shop Module  
AIContentGeneratorService -> Product description
AIImageGenerationService -> Product image
ShopSearchService -> Product search in chat

// Portfolio Module
AIImageGenerationService -> Project cover image
```

## 11. Key Algorithms & Patterns

### Pattern 1: Workflow Execution
```
Flow Definition (JSON) 
→ FlowExecutor.execute()
  → Discover parallel groups
  → Execute nodes sequentially/parallel
  → Track visited nodes (prevent loops)
  → Build context gradually
  → Extract final response
→ Return to controller/API
```

### Pattern 2: Content Generation
```
Module Component
→ generateAIContent() (trait method)
  → Extract editorial brief
  → Build module context
  → Enhance with file analysis (if PDF)
  → Create master prompt
  → AIService.processRequest()
    → Provider selection
    → Credit check
    → Send to AI
    → Stream response
  → Save to database
  → Deduct credits
→ Return generated content
```

### Pattern 3: Translation
```
translateText(text, from, to)
→ Check: empty? same language? skip
→ Build UltraAssertivePrompt (zero-refusal)
→ AIService.processRequest()
  → Temperature: 0.3 (deterministic)
  → Max tokens: 4000+
  → Context: translation
→ Parse response
→ Validate output
→ Return (or fallback to original)
```

### Pattern 4: Silent Fallback
```
Try Primary Provider
  ↓ fails
Try Fallback Provider (auto-selected)
  ↓ fails
Try Cloud Fallback (if configured)
  ↓ all fail
Graceful degradation (return empty/default)
```

## 12. Performance Metrics

### Benchmarks (2025-11-30)
```
CPU Load: 18.44 → 7.09 (↓ 61%)
Horizon Processes: 112 → 38 (↓ 66%)
Query Count (Currency): 1,440 → 0 (↓ 100%)
Query Count (Settings): 700+ → 2 (↓ 99.7%)
Site Response Time: 45s → 2-3s (↑ 15-22x faster)
```

### Optimizations Applied
- Tenant-aware caching with automatic invalidation
- Prompt caching (frequently used prompts)
- Database query optimization (indexes)
- N+1 query fixes
- Smart chunking for large texts
- Parallel node execution in workflows
- Background queue for heavy operations

## 13. Error Handling & Logging

### Log Channels
```
logs/ai-system.log - AI service logs
logs/ai-chat.log - Chat-specific logs
logs/ai-image.log - Image generation logs
logs/ai-translation.log - Translation logs
logs/ai-workflow.log - Workflow execution logs
logs/ai-credit.log - Credit transactions
```

### Exception Hierarchy
```
AICreditException - Credit issues
AdvancedSeoIntegrationException - SEO errors
BulkOperationException - Bulk op errors
DatabaseLearningException - Learning errors
FormProcessingException - Form validation
ProviderMultiplierException - Provider issues
UniversalInputSystemException - UIS errors
```

## 14. Testing Strategy

### Test Components
- **AITestPanel** - Manual prompt testing
- **BulkOperationProcessor** - Batch operation testing
- **SilentFallbackService** - Provider failover testing
- **TelegramNotificationService** - Notification testing

### Test Coverage Areas
- Provider selection & failover
- Credit calculation & deduction
- Content generation quality
- Translation accuracy
- SEO scoring
- Workflow execution
- Image generation
- Context building

## 15. Future Considerations

### Planned Features
- [ ] Multi-provider load balancing
- [ ] Advanced caching with Redis
- [ ] Real-time collaboration in workflows
- [ ] Advanced analytics & reporting
- [ ] Custom model training
- [ ] Vector embeddings for semantic search
- [ ] Advanced prompt versioning
- [ ] A/B testing framework

### Technical Debt
- Refactor 240+ services into smaller modules
- Consolidate translation services (3 variations)
- Optimize database queries further
- Improve test coverage
- Document API more thoroughly

