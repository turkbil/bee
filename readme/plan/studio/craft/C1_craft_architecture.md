# 🏗️ CRAFT.JS ARCHITECTURE - Sistem Mimarisi

## 🎯 Teknoloji Stack

### Frontend Core
```typescript
• React 18+ (Latest)
• TypeScript (Type safety)
• Craft.js (Visual editor core)
• Tailwind CSS (Design system)
• Vite (Build tool)
```

### State Management
```typescript
• Zustand (Lightweight state)
• React Query (Server state)
• Immer (Immutable updates)
```

### UI Components
```typescript
• Radix UI (Headless components)
• React DnD (Drag & drop)
• Framer Motion (Animations)
• React Virtual (Large lists)
```

## 🎨 Visual Editor Architecture

### Core Editor Structure
```typescript
interface StudioEditor {
  // Canvas - Ana editör alanı
  canvas: {
    components: Component[]
    selectedId: string | null
    hoveredId: string | null
    viewport: 'desktop' | 'tablet' | 'mobile'
  }
  
  // Widget Library - Sürüklenebilir componentler
  widgets: {
    categories: WidgetCategory[]
    components: WidgetComponent[]
    search: string
    filters: FilterState
  }
  
  // Property Panel - Seçili component ayarları
  properties: {
    componentId: string
    activeTab: 'design' | 'settings' | 'advanced'
    values: PropertyValues
  }
  
  // Layers Panel - Component hierarchy
  layers: {
    tree: LayerNode[]
    expanded: string[]
    selected: string | null
  }
}
```

### Component System
```typescript
// Base Widget Interface
interface WidgetComponent {
  id: string
  name: string
  category: string
  icon: ReactNode
  preview: string // Base64 image
  
  // Drag & drop metadata
  craft: {
    displayName: string
    props: DefaultProps
    rules: ComponentRules
    related: RelatedComponents
  }
  
  // Responsive settings
  responsive: {
    desktop: ComponentProps
    tablet: ComponentProps  
    mobile: ComponentProps
  }
  
  // Design tokens
  design: {
    variants: Variant[]
    animations: Animation[]
    interactions: Interaction[]
  }
}

// Widget Factory
const createWidget = (config: WidgetConfig): WidgetComponent => {
  return {
    ...config,
    craft: {
      displayName: config.name,
      props: config.defaultProps,
      rules: {
        canDrag: true,
        canDrop: config.canContainChildren,
        canMoveIn: config.allowedParents,
        canMoveOut: config.allowedChildren
      }
    }
  }
}
```

## 🔧 Integration with Laravel

### API Layer
```typescript
// Studio API Client
class StudioAPI {
  // Widget management
  async getWidgets(): Promise<Widget[]>
  async saveWidget(widget: Widget): Promise<void>
  async deleteWidget(id: string): Promise<void>
  
  // Page content
  async loadPage(id: string): Promise<PageData>
  async savePage(id: string, data: PageData): Promise<void>
  
  // Asset management
  async uploadAsset(file: File): Promise<Asset>
  async getAssets(): Promise<Asset[]>
  
  // Theme system
  async getThemes(): Promise<Theme[]>
  async applyTheme(themeId: string): Promise<void>
}

// Laravel Bridge
interface LaravelBridge {
  // Widget loading from Laravel
  loadWidgetFromBlade(path: string): Promise<WidgetComponent>
  
  // Live preview communication
  updatePreviewFrame(html: string): void
  
  // Asset URL resolution
  resolveAssetUrl(path: string): string
}
```

### iframe Communication
```typescript
// Parent (Craft.js Editor)
const sendToPreview = (data: PreviewMessage) => {
  iframe.contentWindow?.postMessage(data, '*')
}

// Child (Alpine.js Preview)
window.addEventListener('message', (event) => {
  if (event.data.type === 'UPDATE_COMPONENT') {
    Alpine.store('preview').updateComponent(event.data)
  }
})
```

## 📱 Responsive System

### Breakpoint Management
```typescript
const BREAKPOINTS = {
  desktop: { min: 1024, max: Infinity },
  tablet: { min: 768, max: 1023 },
  mobile: { min: 0, max: 767 }
}

interface ResponsiveValue<T> {
  desktop: T
  tablet?: T
  mobile?: T
}

// Usage
const padding: ResponsiveValue<string> = {
  desktop: '2rem',
  tablet: '1.5rem', 
  mobile: '1rem'
}
```

### Viewport Switching
```typescript
const ViewportSwitcher = () => {
  const { viewport, setViewport } = useEditor()
  
  return (
    <ButtonGroup>
      <Button 
        active={viewport === 'desktop'}
        onClick={() => setViewport('desktop')}
      >
        <DesktopIcon />
      </Button>
      {/* tablet, mobile buttons */}
    </ButtonGroup>
  )
}
```

## 🎨 Theme System Integration

### Theme Architecture
```typescript
interface Theme {
  id: string
  name: string
  version: string
  
  // Design tokens
  tokens: {
    colors: ColorPalette
    typography: TypographyScale
    spacing: SpacingScale
    shadows: ShadowScale
    borders: BorderScale
  }
  
  // Component variants
  components: {
    [componentName: string]: ComponentVariants
  }
  
  // Global styles
  globals: {
    css: string
    fonts: FontDefinition[]
  }
}

// Theme provider
const ThemeProvider = ({ theme, children }) => {
  const cssVariables = useMemo(() => 
    generateCSSVariables(theme.tokens), [theme]
  )
  
  return (
    <div style={cssVariables}>
      {children}
    </div>
  )
}
```

## 🚀 Performance Optimizations

### Virtual Rendering
```typescript
// Large component lists
const WidgetLibrary = () => {
  const { widgets } = useWidgets()
  
  return (
    <VirtualList
      items={widgets}
      height={400}
      itemHeight={60}
      renderItem={({ item }) => <WidgetItem widget={item} />}
    />
  )
}
```

### Lazy Loading
```typescript
// Component lazy loading
const LazyWidget = lazy(() => import('./widgets/HeroSection'))

// Asset lazy loading  
const ImageWidget = () => {
  const [loaded, setLoaded] = useState(false)
  
  return (
    <img
      loading="lazy"
      onLoad={() => setLoaded(true)}
      className={loaded ? 'opacity-100' : 'opacity-0'}
    />
  )
}
```

### Memoization
```typescript
// Component tree memoization
const ComponentTree = memo(({ nodes }) => {
  return nodes.map(node => (
    <TreeNode key={node.id} node={node} />
  ))
})

// Property panel memoization
const PropertyPanel = memo(({ componentId }) => {
  const component = useNode(componentId)
  return <PropertyForm component={component} />
})
```

## 🔄 State Management

### Editor State
```typescript
// Zustand store
const useEditorStore = create<EditorState>((set, get) => ({
  // Canvas state
  selectedId: null,
  hoveredId: null,
  viewport: 'desktop',
  
  // Actions
  selectComponent: (id) => set({ selectedId: id }),
  hoverComponent: (id) => set({ hoveredId: id }),
  setViewport: (viewport) => set({ viewport }),
  
  // Complex actions with Immer
  updateComponent: (id, updates) => set(
    produce((state) => {
      const component = state.components[id]
      if (component) {
        Object.assign(component.props, updates)
      }
    })
  )
}))
```

Bu mimari ile **dünya standartlarında** visual editor sistemini hayata geçirebiliriz.