# 🚀 CRAFT.JS IMPLEMENTATION PLAN

## 📅 Development Timeline

### Phase 1: Foundation (Gün 1-2)
```bash
🎯 Core Setup & Basic Architecture
✅ React + TypeScript + Vite setup
✅ Craft.js integration
✅ Basic drag & drop functionality  
✅ Laravel API endpoints
✅ Widget system foundation
```

### Phase 2: Essential Features (Gün 3-5)
```bash
🎯 Visual Editor Core Features
✅ Property panels
✅ Responsive breakpoints
✅ Component tree/layers
✅ Basic widget library
✅ Save/load functionality
```

### Phase 3: Advanced Features (Gün 6-10)
```bash
🎯 Professional Grade Features  
✅ Theme system integration
✅ Advanced layout components
✅ Animation system
✅ Undo/redo system
✅ Collaborative editing
```

### Phase 4: Polish & Optimization (Gün 11-14)
```bash
🎯 Production Ready
✅ Performance optimization
✅ Mobile responsive editing
✅ Advanced interactions
✅ A/B testing integration
✅ Analytics & monitoring
```

## 🛠️ Technical Implementation Steps

### Step 1: Project Structure Setup
```bash
# Frontend structure
studio-editor/
├── src/
│   ├── components/          # React components
│   │   ├── Editor/         # Main editor components
│   │   ├── Widgets/        # Widget components
│   │   ├── Panels/         # Side panels
│   │   └── UI/             # Reusable UI components
│   ├── hooks/              # Custom hooks
│   ├── stores/             # Zustand stores
│   ├── types/              # TypeScript definitions
│   ├── utils/              # Utility functions
│   └── api/                # API client
├── public/                 # Static assets
└── docs/                   # Documentation

# Laravel integration
Modules/Studio/
├── Routes/
│   └── api.php             # API routes
├── Http/
│   └── Controllers/
│       └── API/            # API controllers
├── Services/               # Business logic
└── resources/
    └── views/
        └── craft-editor.blade.php  # React container
```

### Step 2: Core Editor Setup
```typescript
// Main editor component
const StudioEditor: React.FC = () => {
  return (
    <Editor
      resolver={{
        Container,
        Text,
        Button,
        Image,
        // Widget resolver'ları
        ...widgetResolvers
      }}
      onNodesChange={(query) => {
        // Auto-save functionality
        debouncedSave(query.serialize())
      }}
    >
      <div className="studio-layout">
        <Toolbar />
        
        <div className="studio-main">
          <WidgetLibrary />
          <Canvas />
          <PropertyPanel />
        </div>
        
        <LayerPanel />
      </div>
    </Editor>
  )
}
```

### Step 3: Widget System Implementation
```typescript
// Widget factory system
interface WidgetDefinition {
  id: string
  name: string
  category: string
  component: React.ComponentType
  defaultProps: object
  propertySchema: PropertySchema
  preview?: string
}

// Widget registration
const registerWidget = (definition: WidgetDefinition) => {
  // Register with Craft.js
  widgets[definition.id] = {
    craft: {
      displayName: definition.name,
      props: definition.defaultProps,
      rules: {
        canDrag: true,
        canDrop: definition.canContainChildren
      },
      related: {
        settings: () => import(`./widgets/${definition.id}/Settings`)
      }
    }
  }
  
  // Register with widget library
  widgetLibrary.add({
    id: definition.id,
    name: definition.name,
    category: definition.category,
    preview: definition.preview,
    component: definition.component
  })
}

// Auto-registration from Laravel
const autoRegisterWidgets = async () => {
  const widgets = await api.getWidgets()
  
  widgets.forEach(async (widget) => {
    // Dynamic import widget component
    const { default: Component } = await import(
      `./widgets/${widget.path}`
    )
    
    registerWidget({
      id: widget.id,
      name: widget.name,
      category: widget.category,
      component: Component,
      defaultProps: widget.defaultProps,
      propertySchema: widget.propertySchema
    })
  })
}
```

### Step 4: Laravel Integration
```php
<?php
// API Controller
class CraftEditorController extends Controller
{
    public function getWidgets()
    {
        return response()->json([
            'widgets' => Widget::active()->get()->map(function ($widget) {
                return [
                    'id' => $widget->id,
                    'name' => $widget->name,
                    'category' => $widget->category,
                    'path' => $widget->component_path,
                    'defaultProps' => $widget->default_props,
                    'propertySchema' => $widget->property_schema,
                    'preview' => $widget->preview_image
                ];
            })
        ]);
    }
    
    public function savePage(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        
        // Craft.js serialized data
        $craftData = $request->input('craft_data');
        
        // Convert to HTML for frontend
        $html = $this->craftToHtml($craftData);
        
        $page->update([
            'content' => $html,
            'craft_data' => $craftData,
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }
    
    private function craftToHtml($craftData): string
    {
        // Convert Craft.js data to clean HTML
        // This will be rendered in the frontend iframe
        
        return $this->widgetRenderer->render($craftData);
    }
}
```

## 🎨 Widget Development Workflow

### 1. Laravel Widget Definition
```php
<?php
// Database migration
Schema::create('studio_widgets', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category');
    $table->string('component_path');
    $table->json('default_props');
    $table->json('property_schema');
    $table->string('preview_image')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Widget Seeder
Widget::create([
    'name' => 'Hero Section',
    'category' => 'content',
    'component_path' => 'HeroSection',
    'default_props' => [
        'title' => 'Welcome to our site',
        'subtitle' => 'Amazing things happen here',
        'buttonText' => 'Get Started',
        'backgroundImage' => null
    ],
    'property_schema' => [
        'title' => ['type' => 'text', 'label' => 'Title'],
        'subtitle' => ['type' => 'textarea', 'label' => 'Subtitle'],
        'buttonText' => ['type' => 'text', 'label' => 'Button Text'],
        'backgroundImage' => ['type' => 'image', 'label' => 'Background']
    ]
]);
```

### 2. React Widget Component
```typescript
// widgets/HeroSection/index.tsx
import { useNode } from '@craftjs/core'

interface HeroSectionProps {
  title: string
  subtitle: string
  buttonText: string
  backgroundImage?: string
}

export const HeroSection: React.FC<HeroSectionProps> = ({
  title,
  subtitle, 
  buttonText,
  backgroundImage
}) => {
  const {
    connectors: { connect, drag }
  } = useNode()
  
  return (
    <section
      ref={(ref) => connect(drag(ref))}
      className="hero-section"
      style={{
        backgroundImage: backgroundImage 
          ? `url(${backgroundImage})` 
          : undefined
      }}
    >
      <div className="hero-content">
        <h1>{title}</h1>
        <p>{subtitle}</p>
        <button>{buttonText}</button>
      </div>
    </section>
  )
}

// Craft.js configuration
HeroSection.craft = {
  displayName: 'Hero Section',
  props: {
    title: 'Welcome to our site',
    subtitle: 'Amazing things happen here',
    buttonText: 'Get Started'
  },
  rules: {
    canDrag: true,
    canDrop: false
  },
  related: {
    settings: HeroSectionSettings
  }
}
```

### 3. Property Panel Component
```typescript
// widgets/HeroSection/Settings.tsx
import { useNode } from '@craftjs/core'

export const HeroSectionSettings = () => {
  const {
    actions: { setProp },
    props: { title, subtitle, buttonText, backgroundImage }
  } = useNode((node) => ({
    props: node.data.props
  }))
  
  return (
    <div className="widget-settings">
      <div className="form-group">
        <label>Title</label>
        <input
          value={title}
          onChange={(e) => setProp((props) => 
            props.title = e.target.value
          )}
        />
      </div>
      
      <div className="form-group">
        <label>Subtitle</label>
        <textarea
          value={subtitle}
          onChange={(e) => setProp((props) => 
            props.subtitle = e.target.value
          )}
        />
      </div>
      
      <div className="form-group">
        <label>Button Text</label>
        <input
          value={buttonText}
          onChange={(e) => setProp((props) => 
            props.buttonText = e.target.value
          )}
        />
      </div>
      
      <div className="form-group">
        <label>Background Image</label>
        <ImagePicker
          value={backgroundImage}
          onChange={(url) => setProp((props) => 
            props.backgroundImage = url
          )}
        />
      </div>
    </div>
  )
}
```

## 🔄 Development Workflow

### Daily Development Process
```bash
# 1. Frontend Development
cd studio-editor/
npm run dev  # Vite dev server

# 2. Laravel API Development  
php artisan serve
php artisan queue:work

# 3. Testing
npm run test
php artisan test

# 4. Build & Deploy
npm run build
php artisan deploy
```

### Widget Development Cycle
```bash
# 1. Create widget definition in Laravel
php artisan make:widget HeroSection

# 2. Generate React component  
npm run generate:widget HeroSection

# 3. Implement component logic
# Edit: widgets/HeroSection/index.tsx

# 4. Create property panel
# Edit: widgets/HeroSection/Settings.tsx

# 5. Test in editor
npm run dev

# 6. Generate preview image
npm run generate:preview HeroSection

# 7. Deploy
php artisan widget:deploy HeroSection
```

Bu implementasyon planı ile **2 hafta içinde** dünya standartlarında visual editor sistemi hazır olacak!