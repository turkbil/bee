<!-- İçerik Yapısı (Öğe Şeması) -->
<div class="tab-pane fade {{ $formMode === 'items' ? 'active show' : '' }}" id="tab-items">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-lightbulb text-blue me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">İçerik Yapısı Nedir?</h4>
                        <div class="text-muted">
                            İçerik yapısı, widgetınızın içerebileceği dinamik verilerin şablonunu belirler. Örneğin:<br>
                            <ul class="mb-0">
                                <li>Slider widget'ı için slaytların başlık, görsel ve açıklama alanları</li>
                                <li>SSS widget'ı için soru ve cevap alanları</li>
                                <li>Galeri widget'ı için resimler ve açıklamaları</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-layer-group me-2"></i>
                            İçerik Yapısı Yönetimi
                        </h3>
                        @if($widgetId)
                        <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'item_schema']) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-magic me-2"></i>
                            Form Builder ile Düzenle
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($widget['item_schema']))
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fas fa-database fa-4x text-muted"></i>
                        </div>
                        <p class="empty-title">Henüz içerik alanı tanımlanmadı</p>
                        <p class="empty-subtitle text-muted">
                            Form Builder kullanarak widget içerikleri için veri alanları tanımlayabilirsiniz.
                        </p>
                        @if($widgetId)
                        <div class="empty-action">
                            <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'item_schema']) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-magic me-2"></i>
                                Form Builder ile Başla
                            </a>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover">
                            <thead>
                                <tr>
                                    <th>Alan Adı</th>
                                    <th>Etiket</th>
                                    <th>Tip</th>
                                    <th>Zorunlu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($widget['item_schema'] as $index => $field)
                                @if(isset($field['name']) && (!isset($field['hidden']) || !$field['hidden']))
                                <tr>
                                    <td>
                                        <code>{{ $field['name'] }}</code>
                                        @if(isset($field['system']) && $field['system'])
                                        <span class="badge bg-orange ms-1">Sistem</span>
                                        @endif
                                    </td>
                                    <td>{{ $field['label'] ?? 'Tanımsız' }}</td>
                                    <td>
                                        <span class="badge bg-blue-lt">
                                            @switch($field['type'] ?? 'text')
                                                @case('text')
                                                    <i class="fas fa-font me-1"></i> Metin
                                                    @break
                                                @case('textarea')
                                                    <i class="fas fa-align-left me-1"></i> Uzun Metin
                                                    @break
                                                @case('number')
                                                    <i class="fas fa-hashtag me-1"></i> Sayı
                                                    @break
                                                @case('select')
                                                    <i class="fas fa-list me-1"></i> Seçim Kutusu
                                                    @break
                                                @case('checkbox')
                                                    <i class="fas fa-check-square me-1"></i> Onay Kutusu
                                                    @break
                                                @case('image')
                                                    <i class="fas fa-image me-1"></i> Resim
                                                    @break
                                                @case('image_multiple')
                                                    <i class="fas fa-images me-1"></i> Çoklu Resim
                                                    @break
                                                @case('color')
                                                    <i class="fas fa-palette me-1"></i> Renk
                                                    @break
                                                @case('date')
                                                    <i class="fas fa-calendar me-1"></i> Tarih
                                                    @break
                                                @case('time')
                                                    <i class="fas fa-clock me-1"></i> Saat
                                                    @break
                                                @case('email')
                                                    <i class="fas fa-envelope me-1"></i> E-posta
                                                    @break
                                                @case('tel')
                                                    <i class="fas fa-phone me-1"></i> Telefon
                                                    @break
                                                @case('url')
                                                    <i class="fas fa-link me-1"></i> URL
                                                    @break
                                                @default
                                                    {{ $field['type'] ?? 'text' }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($field['required']) && $field['required'])
                                        <span class="badge bg-green">
                                            <i class="fas fa-check me-1"></i> Evet
                                        </span>
                                        @else
                                        <span class="badge bg-gray">
                                            <i class="fas fa-minus me-1"></i> Hayır
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>