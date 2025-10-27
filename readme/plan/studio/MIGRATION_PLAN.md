# 🔄 GRAPES → CRAFT.JS MIGRATION PLAN

## 📋 Geçiş Stratejisi

### 🎯 **Zero Downtime Migration**
```bash
# Mevcut GrapesJS korunarak paralel geliştirme
Modules/Studio/
├── GrapesJS/           # Mevcut sistem (korunur)
├── CraftJS/            # Yeni sistem (paralel)
└── Routes/
    ├── grapes.php      # Eski routes
    └── craft.php       # Yeni routes
```

### 🛡️ **Yedekleme Durumu** ✅
- **Module**: `Modules/Studio_backup_20250901_231551/`
- **Assets**: `public/admin-assets/libs/studio_backup_20250901_231635/`
- **Git commit**: `5b54a332` - Studio security & Craft.js planning

## 📅 **Implementation Timeline**

### **Gün 1-2: Foundation Setup**
```bash
🎯 React + Craft.js Core
├── Frontend project structure
├── Vite + React + TypeScript setup
├── Craft.js basic integration
├── Laravel API routes
└── Basic drag & drop test
```

### **Gün 3-4: Core Features**
```bash
🎯 Essential Editor Features  
├── Widget system foundation
├── Property panels
├── Save/load functionality
├── Canvas management
└── Basic responsive system
```

### **Gün 5-7: Advanced Features**
```bash
🎯 Professional Features
├── Theme system integration
├── Global layout management
├── Advanced widget library
├── Interactive UX components
└── Performance optimization
```

### **Gün 8-9: AI Integration**
```bash
🎯 AI-Powered Features
├── Content generation API
├── SEO optimization assistant
├── Design recommendation system
└── Image generation integration
```

### **Gün 10: Migration & Testing**
```bash
🎯 Production Ready
├── Data migration from GrapesJS
├── Feature parity testing
├── Performance benchmarking
└── User acceptance testing
```

## 🔧 **Technical Migration Steps**

### **Step 1: Project Structure**
```typescript
// New Craft.js structure
studio-craft/
├── src/
│   ├── components/
│   │   ├── Editor/         # Main editor
│   │   ├── Widgets/        # Widget components
│   │   ├── Panels/         # Property panels
│   │   ├── AI/            # AI components
│   │   └── UI/            # Reusable UI
│   ├── hooks/             # Custom hooks
│   ├── stores/            # State management
│   ├── services/          # API services
│   └── types/             # TypeScript types
├── public/
└── package.json
```

### **Step 2: Laravel Integration**
```php
<?php
// New API structure
Route::prefix('studio-craft')->group(function () {
    Route::get('/editor/{module}/{id}/{locale}', 'CraftEditorController@index');
    Route::post('/save', 'CraftEditorController@save');
    Route::get('/widgets', 'WidgetController@index');
    Route::post('/ai/generate', 'AIController@generate');
});

// Fallback to GrapesJS
Route::prefix('studio')->group(function () {
    Route::get('/editor/{module}/{id}/{locale}', 'StudioController@editor');
    // Existing GrapesJS routes
});
```

### **Step 3: Data Migration**
```php
<?php
// Migration service for existing data
class GrapesToCraftMigrationService
{
    public function migratePageData($pageId): array
    {
        // Get existing GrapesJS data
        $grapesData = Page::find($pageId)->grapes_data;
        
        // Convert to Craft.js format
        $craftData = $this->convertGrapesToCraft($grapesData);
        
        // Validate and save
        return $this->saveCraftData($pageId, $craftData);
    }
    
    private function convertGrapesToCraft($grapesData): array
    {
        // Conversion logic from GrapesJS to Craft.js
        // Map widgets, styles, and structure
        return $convertedData;
    }
}
```

### **Step 4: Widget Migration**
```typescript
// Widget adapter for existing widgets
class WidgetMigrationAdapter {
  static adaptGrapesWidget(grapesWidget: any): CraftWidget {
    return {
      id: grapesWidget.id,
      type: this.mapWidgetType(grapesWidget.type),
      props: this.convertProps(grapesWidget.attributes),
      children: grapesWidget.components?.map(child => 
        this.adaptGrapesWidget(child)
      ) || []
    }
  }
  
  private static mapWidgetType(grapesType: string): string {
    const typeMap = {
      'text': 'Typography',
      'image': 'Image', 
      'link': 'Link',
      'button': 'Button'
      // ... other mappings
    }
    
    return typeMap[grapesType] || 'Container'
  }
}
```

## 🔄 **Rollback Plan**

### **Rollback Strategy**
```bash
# If issues arise, quick rollback
1. Switch routes back to GrapesJS
2. Restore from backup if needed
3. Git revert to previous commit

# Commands:
git checkout main
git revert HEAD~1  # Revert to previous commit
cp -r Modules/Studio_backup_*/* Modules/Studio/
```

### **Feature Flag System**
```php
<?php
// Feature flag for gradual rollout
if (config('studio.use_craft_editor', false)) {
    return redirect()->route('studio.craft.editor', $params);
} else {
    return redirect()->route('studio.grapes.editor', $params);
}

// Per-user testing
if (auth()->user()->beta_tester) {
    return $craftEditor;
} else {
    return $grapesEditor;
}
```

## 📊 **Success Metrics**

### **Performance Targets**
- ⚡ **Load Time**: < 2 seconds (vs current ~4s)
- 🚀 **FCP**: < 1 second
- 📱 **Mobile Score**: 90+ (vs current ~70)
- 🎨 **Bundle Size**: < 500KB (vs current ~1.2MB)

### **Feature Parity**
- ✅ **All existing widgets** supported
- ✅ **Theme system** maintained  
- ✅ **Save/load** functionality
- ✅ **Responsive** editing
- ✅ **Multi-language** support

### **New Features**
- 🤖 **AI Content Generation**
- ✨ **Interactive UX** (Figma-level)
- 🎭 **Advanced Theme System**
- 👥 **Real-time Collaboration**
- 📊 **Analytics Integration**

## 🚨 **Risk Mitigation**

### **Identified Risks**
1. **Data Loss**: Comprehensive backups + migration testing
2. **User Resistance**: Training + gradual rollout
3. **Performance**: Benchmark testing + optimization
4. **Browser Compatibility**: Cross-browser testing
5. **API Breaking Changes**: Versioned APIs + backwards compatibility

### **Contingency Plans**
1. **Immediate Rollback**: Feature flag toggle
2. **Partial Rollout**: User-based or route-based switching
3. **Hybrid Mode**: Both editors available during transition
4. **Data Recovery**: Multiple backup layers

## 🎯 **Ready State Checklist**

### **Before Starting Migration**
- [x] ✅ **Backups Created**: Module + Assets + Database
- [x] ✅ **Git Committed**: All changes committed
- [x] ✅ **Documentation**: Complete Craft.js plans ready
- [x] ✅ **Team Aligned**: Development plan approved

### **Ready to Begin**
- [ ] 🟡 **Development Environment**: React + Craft.js setup
- [ ] 🟡 **API Endpoints**: Laravel API ready
- [ ] 🟡 **Database**: Migration tables ready
- [ ] 🟡 **Assets**: Build pipeline configured

---

## 🚀 **STATUS: READY TO BEGIN MIGRATION**

**All backups created, plans documented, and migration strategy defined.**  
**GrapesJS system safely preserved for rollback if needed.**

**When you say "başla", we'll start with Day 1 implementation! 🎨**