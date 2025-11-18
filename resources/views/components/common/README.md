# Common Components

## Favorite Button Component

Universal favori butonu - Tum modellerde kullanilabilir (Blog, Product, Page, vs.)

### Kullanim

```blade
{{-- Temel kullanim --}}
<x-common.favorite-button :model="$blog" />

{{-- Ozellestirmeler --}}
<x-common.favorite-button 
    :model="$product" 
    size="lg" 
    :showText="false" 
/>

{{-- Sadece ikon --}}
<x-common.favorite-button 
    :model="$page" 
    :iconOnly="true" 
/>
```

### Parametreler

- model: (Required) Model instance
- size: sm, md, lg (Varsayilan: md)
- showText: true/false (Varsayilan: true)
- iconOnly: true/false (Varsayilan: false)

### Ornekler

Blog detay:
<x-common.favorite-button :model="$blog" />

Product card (sadece ikon):
<x-common.favorite-button :model="$product" :iconOnly="true" />

Kucuk boyut:
<x-common.favorite-button :model="$blog" size="sm" />
