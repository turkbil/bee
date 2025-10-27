# 🎯 ADVANCED STUDIO FEATURES - İleri Seviye Özellikler

## 🏗️ Layout & Template System

### Global Layout Management
```typescript
interface LayoutSystem {
  // Global template parts
  header: {
    type: 'global' | 'page-specific'
    component: ComponentNode
    locked: boolean
    variants: LayoutVariant[]
  }
  
  footer: {
    type: 'global' | 'page-specific' 
    component: ComponentNode
    locked: boolean
    variants: LayoutVariant[]
  }
  
  // Content areas
  content: {
    zones: ContentZone[]
    constraints: LayoutConstraints
  }
  
  // Sidebar regions
  sidebars: {
    left?: ComponentNode
    right?: ComponentNode
    conditions: DisplayConditions
  }
}

// Global vs Page-specific toggle
const LayoutManager = () => {
  const { layout, updateLayout } = useLayout()
  
  return (
    <div className="layout-manager">
      <div className="layout-section">
        <h3>Header</h3>
        <Toggle
          checked={layout.header.type === 'global'}
          onChange={(global) => 
            updateLayout('header', {
              type: global ? 'global' : 'page-specific'
            })
          }
        />
        <span>Global Header</span>
      </div>
      
      <div className="layout-section">
        <h3>Footer</h3>
        <Toggle
          checked={layout.footer.type === 'global'}
          onChange={(global) => 
            updateLayout('footer', {
              type: global ? 'global' : 'page-specific'  
            })
          }
        />
        <span>Global Footer</span>
      </div>
    </div>
  )
}
```

### Template Inheritance System
```typescript
interface Template {
  id: string
  name: string
  type: 'master' | 'page' | 'partial'
  
  // Template hierarchy
  extends?: string // Parent template ID
  blocks: {
    [blockName: string]: {
      required: boolean
      defaultContent?: ComponentNode
      constraints: BlockConstraints
    }
  }
  
  // Layout structure
  structure: {
    header: TemplateBlock
    content: TemplateBlock[]
    sidebar?: TemplateBlock
    footer: TemplateBlock
  }
}

// Master template example
const masterTemplate: Template = {
  id: 'master',
  name: 'Site Master Template',
  type: 'master',
  structure: {
    header: {
      name: 'site-header',
      locked: true, // Cannot be modified on pages
      global: true  // Same across all pages
    },
    content: [
      {
        name: 'hero-area',
        required: false,
        constraints: { maxComponents: 1 }
      },
      {
        name: 'main-content',
        required: true,
        constraints: { minComponents: 1 }
      }
    ],
    footer: {
      name: 'site-footer', 
      locked: true,
      global: true
    }
  }
}

// Page template inheriting from master
const pageTemplate: Template = {
  id: 'product-page',
  name: 'Product Page',
  type: 'page',
  extends: 'master', // Inherits from master
  blocks: {
    'hero-area': {
      required: true, // Override: make hero required for products
      defaultContent: productHeroDefaults
    },
    'product-gallery': {
      required: true,
      constraints: { 
        allowedWidgets: ['ProductGallery', 'ProductImages']
      }
    }
  }
}
```

## 🧩 Massive Widget Management

### Widget Organization System
```typescript
interface WidgetLibrary {
  // Hierarchical categories
  categories: {
    [categoryId: string]: {
      name: string
      icon: string
      color: string
      parent?: string
      subcategories: string[]
      widgets: string[]
    }
  }
  
  // Widget metadata
  widgets: {
    [widgetId: string]: {
      id: string
      name: string
      description: string
      category: string
      tags: string[]
      
      // Usage context
      contexts: ('header' | 'content' | 'sidebar' | 'footer')[]
      compatibility: string[] // Compatible with which templates
      
      // Performance & complexity
      performance: 'fast' | 'medium' | 'slow'
      complexity: 'simple' | 'advanced' | 'expert'
      
      // Preview & demo
      preview: string
      demoUrl?: string
      documentation?: string
    }
  }
}

// Smart widget search & filtering
const WidgetBrowser = () => {
  const [search, setSearch] = useState('')
  const [filters, setFilters] = useState({
    category: '',
    performance: '',
    complexity: '',
    context: ''
  })
  
  const filteredWidgets = useMemo(() => {
    return widgets.filter(widget => {
      // Text search
      if (search && !widget.name.toLowerCase().includes(search.toLowerCase())) {
        return false
      }
      
      // Category filter
      if (filters.category && widget.category !== filters.category) {
        return false
      }
      
      // Performance filter
      if (filters.performance && widget.performance !== filters.performance) {
        return false  
      }
      
      // Context filter (current editing area)
      const currentContext = getCurrentEditingContext()
      if (!widget.contexts.includes(currentContext)) {
        return false
      }
      
      return true
    })
  }, [search, filters, widgets])
  
  return (
    <div className="widget-browser">
      <SearchInput value={search} onChange={setSearch} />
      <FilterPanel filters={filters} onChange={setFilters} />
      
      <VirtualGrid
        items={filteredWidgets}
        itemHeight={120}
        renderItem={({ item }) => (
          <WidgetCard 
            widget={item}
            onDrag={() => handleWidgetDrag(item)}
          />
        )}
      />
    </div>
  )
}
```

### Widget Variants & Configurations
```typescript
interface WidgetVariant {
  id: string
  name: string
  description: string
  preview: string
  
  // Default configuration for this variant
  defaultProps: object
  
  // Conditional logic
  conditions?: {
    deviceType?: 'desktop' | 'tablet' | 'mobile'
    userRole?: string[]
    pageType?: string[]
    context?: string[]
  }
  
  // Style overrides
  styles?: {
    css: string
    className?: string
  }
}

// Widget with multiple variants
const HeroWidget = {
  id: 'hero-section',
  name: 'Hero Section',
  variants: [
    {
      id: 'minimal',
      name: 'Minimal Hero',
      defaultProps: {
        layout: 'centered',
        backgroundType: 'color',
        showButton: true
      }
    },
    {
      id: 'video-bg',
      name: 'Video Background Hero',
      defaultProps: {
        layout: 'full-width',
        backgroundType: 'video',
        overlay: true
      }
    },
    {
      id: 'split-layout',
      name: 'Split Layout Hero',
      defaultProps: {
        layout: 'split',
        imagePosition: 'right',
        contentAlignment: 'left'
      }
    }
  ]
}

// Variant selector in editor
const VariantSelector = ({ widget }) => {
  const { setProp } = useNode()
  const [selectedVariant, setSelectedVariant] = useState('default')
  
  const applyVariant = (variant) => {
    setProp((props) => {
      Object.assign(props, variant.defaultProps)
    })
    setSelectedVariant(variant.id)
  }
  
  return (
    <div className="variant-selector">
      <h4>Choose Style</h4>
      <div className="variant-grid">
        {widget.variants.map(variant => (
          <div 
            key={variant.id}
            className={`variant-card ${
              selectedVariant === variant.id ? 'active' : ''
            }`}
            onClick={() => applyVariant(variant)}
          >
            <img src={variant.preview} alt={variant.name} />
            <span>{variant.name}</span>
          </div>
        ))}
      </div>
    </div>
  )
}
```

## 🎨 Advanced Theme System

### Multi-Theme Architecture
```typescript
interface ThemeSystem {
  // Active themes
  themes: {
    [themeId: string]: Theme
  }
  
  // Theme inheritance
  inheritance: {
    [childTheme: string]: string // parent theme
  }
  
  // Context-specific themes
  contexts: {
    [context: string]: {
      activeTheme: string
      overrides?: Partial<Theme>
    }
  }
  
  // Theme switching rules
  rules: {
    seasonal?: SeasonalThemeRule[]
    userBased?: UserThemeRule[]
    deviceBased?: DeviceThemeRule[]
    contextBased?: ContextThemeRule[]
  }
}

interface Theme {
  id: string
  name: string
  version: string
  author: string
  
  // Design system
  tokens: {
    // Color system
    colors: {
      primary: ColorScale
      secondary: ColorScale
      neutral: ColorScale
      semantic: {
        success: string
        warning: string  
        error: string
        info: string
      }
    }
    
    // Typography system
    typography: {
      fontFamilies: {
        primary: FontDefinition
        secondary: FontDefinition
        mono: FontDefinition
      }
      scales: {
        desktop: TypographyScale
        tablet: TypographyScale
        mobile: TypographyScale
      }
    }
    
    // Spacing system
    spacing: {
      scale: number[] // [4, 8, 12, 16, 24, 32, 48, 64, 96, 128]
      sections: {
        tight: string
        normal: string
        loose: string
      }
    }
    
    // Component styling
    components: {
      [componentName: string]: ComponentTheme
    }
  }
  
  // Global styles
  globals: {
    css: string
    fonts: FontDefinition[]
    assets: AssetDefinition[]
  }
  
  // Theme capabilities
  features: {
    darkMode: boolean
    rtlSupport: boolean
    customColors: boolean
    animations: boolean
  }
}

// Theme manager component
const ThemeManager = () => {
  const { activeTheme, availableThemes, switchTheme } = useTheme()
  
  return (
    <div className="theme-manager">
      <div className="theme-selector">
        <h3>Active Theme</h3>
        <ThemeCard theme={activeTheme} />
      </div>
      
      <div className="theme-library">
        <h3>Available Themes</h3>
        <div className="theme-grid">
          {availableThemes.map(theme => (
            <ThemeCard
              key={theme.id}
              theme={theme}
              onClick={() => switchTheme(theme.id)}
              active={theme.id === activeTheme.id}
            />
          ))}
        </div>
      </div>
      
      <div className="theme-customizer">
        <h3>Customize Current Theme</h3>
        <ColorCustomizer />
        <TypographyCustomizer />
        <SpacingCustomizer />
      </div>
    </div>
  )
}
```

### Theme Inheritance & Overrides
```typescript
// Base theme
const baseTheme: Theme = {
  id: 'base',
  name: 'Base Theme',
  tokens: {
    colors: {
      primary: {
        50: '#f0f9ff',
        500: '#3b82f6',
        900: '#1e3a8a'
      }
    }
  }
}

// Child theme inheriting from base
const corporateTheme: Theme = {
  id: 'corporate',
  name: 'Corporate Theme',
  extends: 'base', // Inherits from base theme
  tokens: {
    colors: {
      primary: {
        // Override primary colors only
        500: '#1f2937', // Corporate gray
        900: '#111827'
      }
      // All other colors inherited from base
    },
    typography: {
      // Add corporate typography
      fontFamilies: {
        primary: {
          name: 'Helvetica Neue',
          weights: [400, 500, 600, 700]
        }
      }
    }
  }
}

// Theme resolution system
const resolveTheme = (themeId: string): ResolvedTheme => {
  const theme = themes[themeId]
  
  if (theme.extends) {
    const parentTheme = resolveTheme(theme.extends)
    
    // Deep merge parent and child themes
    return deepMerge(parentTheme, theme)
  }
  
  return theme
}
```

## 🔧 Advanced Page Organization

### Page Layout Templates
```typescript
interface PageLayout {
  id: string
  name: string
  type: 'layout' | 'template' | 'partial'
  
  // Layout structure
  areas: {
    [areaName: string]: {
      type: 'header' | 'content' | 'sidebar' | 'footer'
      width?: ResponsiveValue<string>
      height?: ResponsiveValue<string>
      constraints: AreaConstraints
      defaultWidgets?: string[]
    }
  }
  
  // Grid system
  grid: {
    columns: ResponsiveValue<number>
    rows: ResponsiveValue<number>
    gaps: ResponsiveValue<string>
    areas: ResponsiveValue<string[][]>
  }
}

// Layout examples
const layouts = {
  'single-column': {
    name: 'Single Column',
    areas: {
      header: { type: 'header', constraints: { sticky: true }},
      content: { type: 'content', constraints: { minHeight: '60vh' }},
      footer: { type: 'footer' }
    },
    grid: {
      columns: { desktop: 1, tablet: 1, mobile: 1 },
      areas: {
        desktop: [
          ['header'],
          ['content'], 
          ['footer']
        ]
      }
    }
  },
  
  'sidebar-right': {
    name: 'Content + Right Sidebar',
    areas: {
      header: { type: 'header' },
      content: { 
        type: 'content',
        width: { desktop: '70%', tablet: '100%', mobile: '100%' }
      },
      sidebar: { 
        type: 'sidebar',
        width: { desktop: '30%', tablet: '100%', mobile: '100%' }
      },
      footer: { type: 'footer' }
    },
    grid: {
      columns: { desktop: 12, tablet: 12, mobile: 1 },
      areas: {
        desktop: [
          ['header', 'header'],
          ['content', 'sidebar'],
          ['footer', 'footer']
        ],
        tablet: [
          ['header'],
          ['content'],
          ['sidebar'],
          ['footer']
        ]
      }
    }
  }
}
```

### Conditional Content System
```typescript
interface ContentCondition {
  id: string
  name: string
  type: 'device' | 'user' | 'time' | 'location' | 'custom'
  
  rules: ConditionRule[]
  action: 'show' | 'hide' | 'replace'
  
  // Alternative content for failed conditions
  fallback?: ComponentNode
}

interface ConditionRule {
  field: string
  operator: 'equals' | 'not_equals' | 'contains' | 'greater_than' | 'less_than'
  value: any
  logicalOperator?: 'AND' | 'OR'
}

// Conditional widget wrapper
const ConditionalWidget = ({ children, conditions }) => {
  const shouldShow = useConditionalLogic(conditions)
  
  if (!shouldShow.result) {
    return shouldShow.fallback || null
  }
  
  return children
}

// Usage in editor
const EditorWithConditions = () => {
  const { selectedNode, updateNode } = useEditor()
  
  return (
    <div className="conditional-editor">
      <h4>Display Conditions</h4>
      
      <ConditionBuilder
        conditions={selectedNode.conditions}
        onChange={(conditions) => 
          updateNode(selectedNode.id, { conditions })
        }
      />
      
      <div className="condition-preview">
        <h5>Preview in:</h5>
        <DevicePreview />
        <UserRolePreview />
        <TimePreview />
      </div>
    </div>
  )
}
```

Bu gelişmiş özellikler ile **enterprise-grade** visual editor sistemi oluşturabiliriz!
- ✅ Global header/footer yönetimi
- ✅ Yüzlerce widget organizasyonu  
- ✅ Çoklu tema sistemi
- ✅ Gelişmiş sayfa yapılandırma
- ✅ Koşullu içerik gösterimi