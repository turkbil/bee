# 🗄️ Database Migration Status Analysis
**Date:** 2025-11-05
**System:** Multi-Tenant AI Chat
**Current State:** HYBRID (Transitional)

---

## 📊 MIGRATION OVERVIEW

### Current Status: **INCOMPLETE MIGRATION**

The database is in a **transitional state** with both OLD and NEW system columns coexisting:

```sql
-- ai_conversations table has BOTH systems' columns:
OLD SYSTEM                  | NEW SYSTEM
---------------------------|---------------------------
feature_slug VARCHAR(255)   | flow_id BIGINT
feature_name VARCHAR(255)   | current_node_id VARCHAR(50)
is_active TINYINT(1)       | state_history JSON
prompt_id BIGINT           | context_data JSON
metadata LONGTEXT          | (uses context_data)
```

---

## 🔍 TABLE-BY-TABLE ANALYSIS

### 1. `ai_conversations` (CENTRAL DB)
**Status:** ⚠️ HYBRID TABLE

```sql
DESC laravel.ai_conversations;

-- OLD SYSTEM COLUMNS (Still in use):
feature_slug      -- 'shop-assistant', 'content-writer', etc.
feature_name      -- Human readable feature name
is_active         -- Active/inactive flag
prompt_id         -- Links to old prompt system
metadata          -- LONGTEXT for old system data

-- NEW SYSTEM COLUMNS (Ready but not active):
flow_id           -- Links to tenant_conversation_flows
current_node_id   -- Current position in flow (e.g., 'node_3')
state_history     -- JSON array of state transitions
context_data      -- JSON object with conversation context

-- SHARED COLUMNS (Used by both):
id, tenant_id, user_id, session_id, created_at, updated_at
```

**Migration Impact:**
- ✅ Backward compatible (OLD system still works)
- ⚠️ Increased table size (duplicate data)
- ❌ Confusing which columns to use

### 2. `ai_workflow_nodes` (CENTRAL DB)
**Status:** ✅ FULLY MIGRATED

```sql
-- Current structure (NEW system only):
CREATE TABLE ai_workflow_nodes (
    id BIGINT PRIMARY KEY,
    type VARCHAR(50),          -- 'product_search', 'ai_response', etc.
    name VARCHAR(255),          -- Display name
    description TEXT,
    category VARCHAR(50),       -- 'shop', 'common', 'utility'
    node_class VARCHAR(255),    -- PHP class path
    is_global BOOLEAN,          -- Available to all tenants?
    is_active BOOLEAN,
    tenant_whitelist JSON,      -- Specific tenant access
    config_schema JSON,         -- Node configuration schema
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Data status:
SELECT COUNT(*) as total,
       SUM(is_global) as global_nodes,
       SUM(!is_global) as tenant_specific
FROM ai_workflow_nodes;
-- Result: 19 total, 19 global, 0 tenant-specific
```

### 3. `tenant_conversation_flows` (TENANT DB)
**Status:** ✅ NEW SYSTEM ONLY

```sql
-- Structure:
CREATE TABLE tenant_conversation_flows (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    flow_name VARCHAR(255),
    flow_description TEXT,
    flow_data JSON,            -- Drawflow format
    start_node_id VARCHAR(50), -- Entry point
    is_active BOOLEAN,
    version INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Check active flows:
SELECT tenant_id, flow_name, is_active,
       JSON_LENGTH(flow_data->'$.nodes') as node_count
FROM tenant_conversation_flows
WHERE tenant_id = 2;
```

### 4. `ai_conversation_messages` (CENTRAL DB)
**Status:** ✅ COMPATIBLE WITH BOTH

```sql
-- Works with both systems:
CREATE TABLE ai_conversation_messages (
    id BIGINT PRIMARY KEY,
    conversation_id BIGINT,    -- Links to ai_conversations
    role ENUM('user','assistant','system'),
    content TEXT,
    tokens_used INT,
    created_at TIMESTAMP
);
```

### 5. `ai_tenant_directives` (CENTRAL DB)
**Status:** ✅ NEW SYSTEM OPTIMIZED

```sql
-- Tenant-specific AI instructions:
CREATE TABLE ai_tenant_directives (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    directive_type VARCHAR(50),
    directive_content TEXT,
    is_active BOOLEAN,
    priority INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 📈 MIGRATION PROGRESS

### Completed ✅
- [x] Node registry table created
- [x] Nodes migrated from TenantSpecific to Shop namespace
- [x] Flow table created in tenant DB
- [x] AIConversation model updated to support both systems
- [x] NodeExecutor implemented with tenant awareness
- [x] ConversationFlowEngine fully implemented

### In Progress ⚠️
- [ ] ai_conversations table using both column sets
- [ ] Config toggle not working properly
- [ ] V2 system not returning responses

### Not Started ❌
- [ ] Remove OLD system columns from ai_conversations
- [ ] Migrate existing conversations to flow format
- [ ] Remove legacy prompt_id system
- [ ] Clean up feature_slug/feature_name columns

---

## 🔄 MIGRATION PATH

### Phase 1: Current State (NOW)
- Both systems coexist
- Config toggle switches between them
- No data loss risk

### Phase 2: Parallel Run (NEXT)
1. Fix V2 system response issue
2. Run both systems in parallel
3. Compare outputs for validation
4. Log performance metrics

### Phase 3: V2 Primary (FUTURE)
1. Make V2 default
2. Keep V1 as fallback
3. Monitor for issues
4. Gradual migration of old conversations

### Phase 4: V1 Removal (FINAL)
1. Drop OLD columns:
   ```sql
   ALTER TABLE ai_conversations
   DROP COLUMN feature_slug,
   DROP COLUMN feature_name,
   DROP COLUMN is_active,
   DROP COLUMN prompt_id,
   DROP COLUMN metadata;
   ```

2. Remove V1 code:
   - `shopAssistantChat()` method
   - Old orchestrator system
   - Legacy services

---

## 🚨 RISKS & MITIGATIONS

### Risk 1: Data Loss
**Mitigation:** Keep OLD columns until V2 proven stable

### Risk 2: Performance Impact
**Mitigation:** Add indexes on new columns
```sql
ALTER TABLE ai_conversations
ADD INDEX idx_flow_id (flow_id),
ADD INDEX idx_current_node (current_node_id);
```

### Risk 3: Tenant Confusion
**Mitigation:** Clear tenant_id tracking in all tables

### Risk 4: Rollback Difficulty
**Mitigation:** Maintain backward compatibility

---

## 📋 MIGRATION CHECKLIST

### Immediate Actions
- [ ] Backup ai_conversations table
- [ ] Document which conversations use which system
- [ ] Create migration rollback plan

### Before V2 Activation
- [ ] Verify all tenants have active flows
- [ ] Test flow has AI Response nodes
- [ ] Confirm node registry populated
- [ ] Check conversation creation logic

### After V2 Stable
- [ ] Archive OLD system data
- [ ] Plan column removal
- [ ] Update documentation
- [ ] Remove OLD system code

---

## 🔍 VERIFICATION QUERIES

```sql
-- 1. Check hybrid usage
SELECT
    COUNT(*) as total,
    SUM(feature_slug IS NOT NULL) as old_system,
    SUM(flow_id IS NOT NULL) as new_system,
    SUM(feature_slug IS NOT NULL AND flow_id IS NOT NULL) as both
FROM ai_conversations
WHERE tenant_id = 2;

-- 2. Recent conversation distribution
SELECT
    DATE(created_at) as date,
    SUM(feature_slug = 'shop-assistant') as old_shop,
    SUM(flow_id IS NOT NULL) as new_flow
FROM ai_conversations
WHERE tenant_id = 2
  AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at);

-- 3. Active flow check
SELECT
    t.id as tenant_id,
    t.name as tenant_name,
    COUNT(f.id) as flow_count,
    SUM(f.is_active) as active_flows
FROM tenants t
LEFT JOIN tenant_conversation_flows f ON t.id = f.tenant_id
GROUP BY t.id;

-- 4. Node registry status
SELECT
    category,
    COUNT(*) as node_count,
    GROUP_CONCAT(type) as node_types
FROM ai_workflow_nodes
WHERE is_active = 1
GROUP BY category;
```

---

## 💡 RECOMMENDATIONS

1. **Fix V2 First:** Don't migrate data until V2 working
2. **Keep Hybrid:** Maintain both systems during transition
3. **Monitor Performance:** Track query times on hybrid table
4. **Document Everything:** Log which system each conversation uses
5. **Test Thoroughly:** Each tenant needs validation

---

## 📊 STATISTICS

- **Total Conversations:** ~500+ across all tenants
- **Daily New Conversations:** ~50-100
- **Table Size:** ai_conversations ~10MB
- **Migration Complexity:** MEDIUM (due to active production use)
- **Rollback Difficulty:** LOW (backward compatible)

---

**Conclusion:** The migration is ~60% complete. The infrastructure is ready but the execution is blocked by the V2 response issue. Once fixed, migration can proceed safely.