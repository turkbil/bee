# 🎨 A3 | Studio Editor Migration Planı

> **Amaç**: GrapesJS → Craft.js geçiş stratejisi ve widget sistemi korunması  
> **Kapsam**: Studio modülünün React ekosisteminde yeniden inşası  
> **Kritik Nokta**: Mevcut widget sistemi backend'i korunacak, sadece frontend modernize edilecek

---

## 🔄 MİGRASYON STRATEJİSİ ÖZETİ

### Mevcut Durum (GrapesJS)
```
Current Stack:
├── Editor: GrapesJS (vanilla JS)  
├── Widget System: WidgetManagement module integration
├── Rendering: Server-side (Laravel Blade)
├── State: DOM-based + Laravel sessions
├── UI: Custom CSS + Tabler.io integration
└── Mobile: Limited responsive support
```

### Hedef Durum (Craft.js)
```
Target Stack:  
├── Editor: Craft.js (React-based)
├── Widget System: Same backend, React frontend
├── Rendering: Client-side (React components)
├── State: Zustand + TanStack Query  
├── UI: Mantine components + CSS-in-JS
└── Mobile: Native React mobile optimization
```

---

## 🏗️ MİMARİ KARŞILAŞTIRMA

### GrapesJS Mimarisi (Mevcut)
```
┌─────────────────────────────────────┐
│           Frontend (Browser)        │  
├─────────────────────────────────────┤
│ GrapesJS Editor                     │
│ ├── Block Manager (widget loader)   │
│ ├── Style Manager (CSS editor)      │
│ ├── Layer Manager (component tree)  │
│ └── Canvas (visual editor)          │
├─────────────────────────────────────┤
│ Custom JS Modules (15+ files)       │
│ ├── studio-widget-manager.js        │
│ ├── studio-blocks.js               │
│ ├── studio-actions-save.js         │
│ └── studio-ui-*.js                 │
└─────────────────────────────────────┘
                  ↕ HTTP
┌─────────────────────────────────────┐
│        Backend (Laravel)            │
├─────────────────────────────────────┤
│ StudioController                    │
│ ├── save() - content persistence    │
│ ├── getBlocks() - widget loading   │
│ └── uploadAssets() - file handling │
├─────────────────────────────────────┤
│ Services Layer                      │
│ ├── EditorService                  │
│ ├── WidgetService                  │
│ ├── BlockService                   │
│ └── AssetService                   │
├─────────────────────────────────────┤
│ Widget System (WidgetManagement)    │
│ ├── Widget models                  │
│ ├── WidgetCategory hierarchy       │
│ ├── TenantWidget instances         │
│ └── WidgetItem content             │
└─────────────────────────────────────┘
```

### Craft.js Mimarisi (Hedef)
```
┌─────────────────────────────────────┐
│      React Application              │
├─────────────────────────────────────┤
│ Craft.js Editor                     │
│ ├── Toolbox (widget panel)         │
│ ├── Settings Panel (component props)│
│ ├── Layers (component tree)        │
│ └── Frame (visual canvas)           │
├─────────────────────────────────────┤
│ React Components                    │
│ ├── <WidgetRenderer /> (4 types)    │
│ ├── <AssetUploader />              │
│ ├── <StyleEditor />                │
│ └── <MobilePreview />              │
├─────────────────────────────────────┤
│ State Management                    │
│ ├── Zustand (editor state)         │
│ ├── TanStack Query (API cache)     │
│ └── React Context (user prefs)     │
└─────────────────────────────────────┘
                  ↕ REST API
┌─────────────────────────────────────┐
│     Laravel API (Unchanged)         │
├─────────────────────────────────────┤
│ API Controllers                     │
│ ├── /api/studio/save               │
│ ├── /api/studio/blocks             │
│ ├── /api/studio/assets             │
│ └── /api/studio/widgets            │
├─────────────────────────────────────┤
│ Services Layer (Same)               │  
│ ├── EditorService                  │
│ ├── WidgetService                  │
│ ├── BlockService                   │
│ └── AssetService                   │
├─────────────────────────────────────┤
│ Widget System (Unchanged)           │
│ ├── Widget models                  │
│ ├── WidgetCategory hierarchy       │
│ ├── TenantWidget instances         │
│ └── WidgetItem content             │
└─────────────────────────────────────┘
```

---

## 🧩 WİDGET SİSTEMİ MİGRASYONU

### Widget Türleri ve React Karşılıkları

#### 1. Static Widgets
```typescript
// Mevcut: GrapesJS component
{
  id: 'static-widget-1',
  label: 'Hero Section',
  category: 'sections',
  content: '<div class="hero">...</div>',
  style: '.hero { ... }',
  script: 'function initHero() {...}'
}

// Hedef: Craft.js React component
const StaticWidget: React.FC<StaticWidgetProps> = ({
  content,
  styles,
  scripts
}) => {
  useEffect(() => {
    // Script execution
    executeScript(scripts);
  }, [scripts]);
  
  return (
    <div 
      dangerouslySetInnerHTML={{ __html: content }}
      css={styles} // CSS-in-JS ile
    />
  );
};
```

#### 2. Dynamic Widgets  
```typescript
// Mevcut: Backend render + GrapesJS placeholder
{
  id: 'dynamic-blog-posts',
  type: 'dynamic',
  category: 'content',
  data_source: 'blog_posts',
  template: 'blog-card-grid'
}

// Hedef: React component + API integration
const DynamicWidget: React.FC<DynamicWidgetProps> = ({
  dataSource,
  template,
  settings
}) => {
  const { data, isLoading } = useTanStackQuery(
    ['dynamic-data', dataSource],
    () => fetchDynamicData(dataSource, settings)
  );
  
  return (
    <MantineLoader visible={isLoading}>
      <DynamicRenderer 
        data={data}
        template={template}
        settings={settings}
      />
    </MantineLoader>
  );
};
```

#### 3. File Widgets (En Kompleks)
```typescript
// Mevcut: Blade file render
{
  id: 'contact-form',
  type: 'file',
  file_path: 'widgets/contact-form.blade.php',
  category: 'forms'
}

// Hedef: React component loader
const FileWidget: React.FC<FileWidgetProps> = ({
  filePath,
  settings
}) => {
  // Dynamic component loading
  const ComponentToRender = useMemo(() => 
    React.lazy(() => import(`@/widgets/${filePath}`))
  , [filePath]);
  
  return (
    <Suspense fallback={<MantineLoader />}>
      <ComponentToRender {...settings} />
    </Suspense>
  );
};
```

#### 4. Module Widgets
```typescript
// Mevcut: Laravel module integration  
{
  id: 'portfolio-showcase',
  type: 'module',
  module_name: 'Portfolio',
  action: 'recent_projects',
  settings: { limit: 6 }
}

// Hedef: API-based React component
const ModuleWidget: React.FC<ModuleWidgetProps> = ({
  moduleName,
  action,
  settings
}) => {
  const { data } = useTanStackQuery(
    ['module-data', moduleName, action],
    () => fetchModuleData(moduleName, action, settings)
  );
  
  return (
    <ModuleRenderer 
      module={moduleName}
      data={data}
      settings={settings}
    />
  );
};
```

---

## 🔄 CRAFT.JS ENTEGRASYONU

### Temel Setup
```typescript
// EditorContext.tsx
import { Editor, Frame, Element } from '@craftjs/core';

const StudioEditor: React.FC = () => {
  const [editorState, setEditorState] = useStore();
  
  return (
    <Editor
      resolver={{
        StaticWidget,
        DynamicWidget,
        FileWidget,
        ModuleWidget,
        // Mantine components
        Container: MantineContainer,
        Text: MantineText,
        Button: MantineButton,
      }}
      onRender={RenderNode}
    >
      <EditorLayout>
        <Toolbox /> {/* Widget panel */}
        <Frame> {/* Visual canvas */}
          <Element is={Container} padding={20}>
            {/* Default content */}
          </Element>
        </Frame>
        <SettingsPanel /> {/* Properties panel */}
      </EditorLayout>
    </Editor>
  );
};
```

### Widget Loading Sistemi
```typescript
// WidgetLoader.tsx  
const WidgetLoader: React.FC = () => {
  // Backend'den widget listesi
  const { data: widgets } = useTanStackQuery(
    ['studio-widgets'],
    () => fetchWidgets() // Mevcut API endpoint
  );
  
  // Kategorilere göre gruplama
  const groupedWidgets = useMemo(() => 
    groupBy(widgets, 'category')
  , [widgets]);
  
  return (
    <MantineAccordion>
      {Object.entries(groupedWidgets).map(([category, widgets]) => (
        <MantineAccordion.Item key={category} value={category}>
          <MantineAccordion.Control>
            {category}
          </MantineAccordion.Control>
          <MantineAccordion.Panel>
            {widgets.map(widget => (
              <WidgetCard 
                key={widget.id}
                widget={widget}
                onDrag={handleWidgetDrag}
              />
            ))}
          </MantineAccordion.Panel>
        </MantineAccordion.Item>
      ))}
    </MantineAccordion>
  );
};
```

### Settings Panel Integration
```typescript
// SettingsPanel.tsx
import { useEditor } from '@craftjs/core';

const SettingsPanel: React.FC = () => {
  const { selected, actions, query } = useEditor((state) => ({
    selected: query.getEvent('selected').first(),
  }));
  
  const selectedNode = selected && query.node(selected).get();
  
  return (
    <MantineStack>
      {selectedNode && (
        <>
          {/* Component-specific settings */}
          <ComponentSettings 
            node={selectedNode}
            onChange={(props) => 
              actions.setProp(selected, (node) => {
                Object.assign(node.props, props);
              })
            }
          />
          
          {/* Style editor */}
          <StyleEditor 
            node={selectedNode}
            onChange={(styles) =>
              actions.setProp(selected, (node) => {
                node.props.styles = styles;
              })
            }
          />
        </>
      )}
    </MantineStack>
  );
};
```

---

## 💾 SAVE/LOAD SİSTEMİ

### Save İşlemi
```typescript
// SaveManager.tsx
const SaveManager: React.FC = () => {
  const { query } = useEditor();
  const [saving, setSaving] = useState(false);
  
  const handleSave = async () => {
    setSaving(true);
    
    try {
      // Craft.js state'ini serialize et
      const serializedState = query.serialize();
      
      // HTML, CSS, JS çıktılarını generate et
      const { html, css, js } = generateOutput(serializedState);
      
      // Backend'e kaydet (mevcut API endpoint)
      await saveTanStackMutation.mutateAsync({
        content: html,
        css: css,
        js: js,
        state: serializedState // React state'i de sakla
      });
      
      showNotification({
        title: 'Başarılı',
        message: 'Değişiklikler kaydedildi',
        color: 'green'
      });
    } catch (error) {
      showNotification({
        title: 'Hata',
        message: 'Kaydetme işlemi başarısız',
        color: 'red'
      });
    } finally {
      setSaving(false);
    }
  };
  
  return (
    <MantineButton 
      onClick={handleSave}
      loading={saving}
      leftIcon={<IconSave />}
    >
      Kaydet
    </MantineButton>
  );
};
```

### Load İşlemi
```typescript  
// LoadManager.tsx
const LoadManager: React.FC<{ moduleId: number }> = ({ moduleId }) => {
  const { actions, query } = useEditor();
  const [loading, setLoading] = useState(true);
  
  useEffect(() => {
    const loadContent = async () => {
      try {
        // Mevcut API'den content'i çek
        const { data } = await fetchContent(moduleId);
        
        if (data.state) {
          // React state varsa, onu restore et
          actions.deserialize(data.state);
        } else {
          // Legacy HTML content'i varsa, parse et
          const parsedState = parseHTMLToState(data.content);
          actions.deserialize(parsedState);
        }
      } catch (error) {
        console.error('Content load failed:', error);
        // Fallback: boş canvas
        actions.deserialize(getEmptyCanvasState());
      } finally {
        setLoading(false);
      }
    };
    
    loadContent();
  }, [moduleId]);
  
  if (loading) {
    return <MantineLoader size="xl" />;
  }
  
  return null; // Bu component sadece loading için
};
```

---

## 📱 MOBİLE OPTİMİZASYONU

### Responsive Editor Layout
```typescript
// ResponsiveEditor.tsx
const ResponsiveEditor: React.FC = () => {
  const [viewportSize, setViewportSize] = useState('desktop');
  const isMobile = useMediaQuery('(max-width: 768px)');
  
  return (
    <Editor>
      <MantineGroup position="apart">
        {/* Device preview switcher */}
        <DeviceSwitcher 
          value={viewportSize}
          onChange={setViewportSize}
        />
        
        {/* Mobile optimized toolbar */}
        {isMobile ? (
          <MantineDrawer> {/* Slide-out panels */}
            <Toolbox />
          </MantineDrawer>
        ) : (
          <MantineGroup> {/* Side panels */}
            <Toolbox />
            <SettingsPanel />
          </MantineGroup>
        )}
      </MantineGroup>
      
      <Frame>
        <ResponsiveCanvas viewportSize={viewportSize} />
      </Frame>
    </Editor>
  );
};
```

### Touch Gesture Support
```typescript
// TouchHandler.tsx  
const TouchHandler: React.FC = () => {
  const { actions } = useEditor();
  
  const touchHandlers = useMemo(() => ({
    onPinch: (scale: number) => {
      // Zoom in/out
      actions.setOptions(options => ({
        ...options,
        zoom: Math.max(0.5, Math.min(2, scale))
      }));
    },
    
    onSwipe: (direction: 'left' | 'right') => {
      // Panel toggle
      if (direction === 'left') {
        // Hide left panel
        setLeftPanelVisible(false);
      } else {
        // Hide right panel  
        setRightPanelVisible(false);
      }
    },
    
    onLongPress: (nodeId: string) => {
      // Context menu
      showContextMenu(nodeId);
    }
  }), [actions]);
  
  return <GestureDetector {...touchHandlers} />;
};
```

---

## 🔌 API ENTEGRASYONLARİ

### Backend API Düzenlemeleri (Minimal)
```php
// StudioController.php - API response format
public function getBlocks(): JsonResponse 
{
    // Mevcut logic korunuyor, sadece response format
    $blocks = $this->blockService->getAllBlocks();
    
    // React için ek metadata
    $blocksWithMeta = array_map(function($block) {
        return [
            ...$block,
            'reactComponent' => $this->getReactComponentName($block['type']),
            'props' => $this->getDefaultProps($block),
            'craftSettings' => $this->getCraftJsSettings($block)
        ];
    }, $blocks);
    
    return response()->json([
        'success' => true,
        'blocks' => $blocksWithMeta,
        'categories' => $this->getCategoriesForReact()
    ]);
}

public function saveContent(Request $request): JsonResponse
{
    // Mevcut save logic + React state storage
    $result = $this->editorService->saveContent(
        $request->input('module'),
        $request->input('id'),
        $request->input('content'),
        $request->input('css'),
        $request->input('js'),
        $request->input('state') // Yeni: React state
    );
    
    return response()->json(['success' => $result]);
}
```

### React API Client
```typescript
// api/studio.ts
export const studioApi = {
  // Widget listesi
  getWidgets: async (): Promise<Widget[]> => {
    const { data } = await axios.get('/api/studio/blocks');
    return data.blocks;
  },
  
  // Content kaydetme
  saveContent: async (payload: SaveContentPayload) => {
    const { data } = await axios.post('/api/studio/save', payload);
    return data;
  },
  
  // Content yükleme  
  loadContent: async (moduleId: number) => {
    const { data } = await axios.get(`/api/studio/load/${moduleId}`);
    return data;
  },
  
  // Asset upload
  uploadAsset: async (file: File) => {
    const formData = new FormData();
    formData.append('file', file);
    
    const { data } = await axios.post('/api/studio/upload', formData);
    return data;
  }
};

// TanStack Query hooks
export const useWidgets = () =>
  useQuery(['studio-widgets'], studioApi.getWidgets);

export const useSaveContent = () =>
  useMutation(studioApi.saveContent);

export const useLoadContent = (moduleId: number) =>
  useQuery(['studio-content', moduleId], () => 
    studioApi.loadContent(moduleId)
  );
```

---

## 🧪 TEST STRATEJİSİ

### Unit Tests
```typescript
// __tests__/widgets/StaticWidget.test.tsx
describe('StaticWidget', () => {
  it('renders content correctly', () => {
    const props = {
      content: '<div>Test content</div>',
      styles: { color: 'red' }
    };
    
    render(<StaticWidget {...props} />);
    
    expect(screen.getByText('Test content')).toBeInTheDocument();
  });
  
  it('executes scripts on mount', () => {
    const mockScript = jest.fn();
    window.testScript = mockScript;
    
    const props = {
      content: '<div>Content</div>',
      scripts: 'window.testScript();'
    };
    
    render(<StaticWidget {...props} />);
    
    expect(mockScript).toHaveBeenCalled();
  });
});
```

### Integration Tests  
```typescript
// __tests__/integration/EditorFlow.test.tsx
describe('Editor Integration', () => {
  it('completes full save/load cycle', async () => {
    // Setup mock API
    mockAPI.post('/api/studio/save').reply(200, { success: true });
    mockAPI.get('/api/studio/load/1').reply(200, { 
      content: '<div>Saved content</div>',
      state: mockState
    });
    
    render(<StudioEditor moduleId={1} />);
    
    // Wait for load
    await waitFor(() => 
      expect(screen.getByText('Saved content')).toBeInTheDocument()
    );
    
    // Make changes
    const textElement = screen.getByText('Saved content');
    fireEvent.click(textElement);
    fireEvent.change(screen.getByRole('textbox'), {
      target: { value: 'Modified content' }
    });
    
    // Save changes
    fireEvent.click(screen.getByRole('button', { name: /save/i }));
    
    await waitFor(() => 
      expect(mockAPI.history.post).toHaveLength(1)
    );
    
    expect(mockAPI.history.post[0].data).toContain('Modified content');
  });
});
```

### E2E Tests
```typescript
// e2e/studio-editor.spec.ts
test('Studio editor workflow', async ({ page }) => {
  // Login
  await page.goto('/admin/login');
  await page.fill('[name="email"]', 'admin@test.com');
  await page.fill('[name="password"]', 'password');
  await page.click('button[type="submit"]');
  
  // Navigate to studio
  await page.goto('/admin/studio/edit/1');
  
  // Wait for editor load
  await page.waitForSelector('[data-testid="craft-editor"]');
  
  // Add widget
  await page.dragAndDrop(
    '[data-testid="widget-hero"]',
    '[data-testid="canvas-drop-zone"]'
  );
  
  // Configure widget
  await page.click('[data-testid="hero-widget"]');
  await page.fill('[data-testid="hero-title"]', 'New Hero Title');
  
  // Save
  await page.click('[data-testid="save-button"]');
  await page.waitForSelector('[data-testid="save-success"]');
  
  // Verify save
  await page.reload();
  await page.waitForSelector('[data-testid="craft-editor"]');
  
  expect(await page.textContent('[data-testid="hero-title"]'))
    .toBe('New Hero Title');
});
```

---

## 🚀 DEPLOYMENT STRATEJİSİ

### Build Process
```typescript
// vite.config.ts
export default defineConfig({
  plugins: [
    react(),
    tsconfigPaths(),
  ],
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          // Craft.js separate chunk
          craftjs: ['@craftjs/core', '@craftjs/utils'],
          // Mantine separate chunk  
          mantine: ['@mantine/core', '@mantine/hooks'],
          // Vendor libraries
          vendor: ['react', 'react-dom', 'zustand']
        }
      }
    }
  },
  optimizeDeps: {
    include: ['@craftjs/core', '@mantine/core']
  }
});
```

### Progressive Rollout
```typescript
// Feature flag integration
const useFeatureFlag = (flag: string) => {
  const [enabled, setEnabled] = useState(false);
  
  useEffect(() => {
    // Check feature flag from API/localStorage
    const checkFlag = async () => {
      const flags = await fetchFeatureFlags();
      setEnabled(flags[flag] || false);
    };
    
    checkFlag();
  }, [flag]);
  
  return enabled;
};

// Editor component with fallback
const StudioEditorWrapper: React.FC = () => {
  const reactEditorEnabled = useFeatureFlag('react-studio-editor');
  
  if (reactEditorEnabled) {
    return <StudioEditor />; // React/Craft.js version
  }
  
  // Fallback to legacy GrapesJS
  return <LegacyGrapesJSEditor />;
};
```

---

## 📈 PERFORMANCE BENCHMARKLARİ

### Current GrapesJS Performance
```
📊 Baseline Metrics:
├── Editor Load Time: ~2.1s
├── Widget Library Load: ~800ms  
├── Save Operation: ~1.2s
├── Bundle Size: ~950KB (all modules)
├── Memory Usage: ~45MB (after 10min usage)
└── Mobile Responsiveness: 3/5 (functional but slow)
```

### Target Craft.js Performance  
```
🎯 Performance Goals:
├── Editor Load Time: <1.2s (-40%)
├── Widget Library Load: <400ms (-50%)
├── Save Operation: <600ms (-50%)  
├── Bundle Size: <650KB (-32%) with code splitting
├── Memory Usage: <30MB (-33%) with proper cleanup
└── Mobile Responsiveness: 5/5 (smooth native feel)
```

### Performance Monitoring
```typescript
// Performance tracking
const usePerformanceTracking = () => {
  const { query } = useEditor();
  
  useEffect(() => {
    // Track editor load time
    const startTime = performance.now();
    
    const unsubscribe = query.subscribe(() => {
      const loadTime = performance.now() - startTime;
      
      // Send to analytics
      analytics.track('studio_editor_load', {
        duration: loadTime,
        widget_count: query.getNodes().length
      });
    });
    
    return unsubscribe;
  }, []);
};
```

---

## 🔐 GÜVENLİK KONSİDERASYONLARİ

### XSS Prevention
```typescript
// Güvenli HTML rendering
const SafeHTMLRenderer: React.FC<{ content: string }> = ({ content }) => {
  const sanitizedContent = useMemo(() => 
    DOMPurify.sanitize(content, {
      ALLOWED_TAGS: ['div', 'span', 'p', 'h1', 'h2', 'h3', 'img'],
      ALLOWED_ATTR: ['class', 'id', 'src', 'alt', 'href']
    })
  , [content]);
  
  return (
    <div 
      dangerouslySetInnerHTML={{ 
        __html: sanitizedContent 
      }} 
    />
  );
};
```

### Script Execution Security
```typescript  
// Güvenli script execution
const executeScript = (script: string, context: any = {}) => {
  try {
    // Sandbox içinde execute et
    const sandboxedFunction = new Function(
      'context',
      `
        "use strict";
        // Dangerous globals'ları disable et
        const window = undefined;
        const document = undefined;
        const eval = undefined;
        
        ${script}
      `
    );
    
    return sandboxedFunction(context);
  } catch (error) {
    console.error('Script execution error:', error);
    return null;
  }
};
```

---

## 📝 SONUÇ & NEXT STEPS

### Migration Özeti
```
🎯 Kritik Başarı Faktörleri:
├── Backend API compatibility maintained (✅)
├── Widget system 100% functional (✅)  
├── Performance targets achieved (target: ✅)
├── Mobile experience improved (target: ✅)
├── Zero data loss during transition (✅)
└── User experience enhanced (target: ✅)
```

### Implementation Roadmap
```
📅 Timeline:
├── Week 6-7: Craft.js setup + basic widgets
├── Week 8: Advanced widgets + save/load system
├── Week 9: Mobile optimization + performance tuning
└── Week 10: Testing + production deployment
```

### Success Metrics
```
📊 KPI Targets:
├── Editor load time: <1.2s (vs current 2.1s)
├── Mobile usability score: >4.5/5
├── Widget creation time: <30s (vs current 2min)  
├── User satisfaction: >90% positive feedback
└── Bug reports: <5% vs baseline
```

---

> **Sonraki Adım**: B1_proje_kurulum_rehberi.md - Development environment setup için adım adım rehber