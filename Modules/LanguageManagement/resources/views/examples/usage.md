# Language Switcher Component Kullanımı

## Temel Kullanım

```blade
<!-- Varsayılan dropdown style -->
<x-language-management::language-switcher />

<!-- Button group style -->
<x-language-management::language-switcher style="buttons" />

<!-- Simple links style -->
<x-language-management::language-switcher style="links" />

<!-- Minimal flag-only style -->
<x-language-management::language-switcher style="minimal" />
```

## Özelleştirme Seçenekleri

```blade
<!-- Sadece bayraklar, metin yok -->
<x-language-management::language-switcher 
    style="buttons" 
    :show-text="false" 
    :show-flags="true" />

<!-- Sadece metin, bayrak yok -->
<x-language-management::language-switcher 
    style="dropdown" 
    :show-text="true" 
    :show-flags="false" />

<!-- Linkler halinde minimal görünüm -->
<x-language-management::language-switcher 
    style="links" 
    :show-text="true" 
    :show-flags="true" />
```

## Header'da Kullanım

```blade
<!-- Ana navigasyon bar'da -->
<nav class="navbar">
    <div class="navbar-nav ms-auto">
        <x-language-management::language-switcher style="buttons" />
    </div>
</nav>

<!-- Footer'da -->
<footer class="footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p>&copy; 2024 Turkbil Bee</p>
            </div>
            <div class="col-md-6 text-end">
                <x-language-management::language-switcher style="links" />
            </div>
        </div>
    </div>
</footer>
```

## Mobil Responsive

Component otomatik olarak responsive'dir:
- Desktop: Tam görünüm
- Mobile: Bayrak + kısaltılmış metin
- Çok küçük ekranlar: Sadece bayraklar

## Stil Seçenekleri

1. **dropdown**: Açılır liste (varsayılan)
2. **buttons**: Yan yana butonlar  
3. **links**: Basit linkler
4. **minimal**: Sadece bayrak butonları