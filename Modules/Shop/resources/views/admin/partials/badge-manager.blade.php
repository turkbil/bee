{{-- Badge Yönetimi --}}
<div class="card mt-4" x-data="badgeManager()">
    <div class="card-header">
        <h3 class="mb-0">
            <i class="fas fa-tags me-2"></i>
            Badge Yönetimi
        </h3>
    </div>

    <div class="card-body">

        {{-- Badge Listesi --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Ürün Etiketleri (Badges)</h5>
            <button type="button" class="btn btn-sm btn-primary" @click="addBadge()">
                <i class="fas fa-plus me-1"></i>
                Badge Ekle
            </button>
        </div>

        {{-- Badge Items --}}
        <div class="badge-list">
            <template x-if="!badges || badges.length === 0">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-tag fa-3x mb-3"></i>
                    <p>Henüz badge eklenmemiş</p>
                    <small>Badge ekleyerek ürününüzü öne çıkarın</small>
                </div>
            </template>

            <template x-for="(badge, index) in badges" :key="index">
                <div class="card mb-3 border-start border-4"
                    :class="`border-${badge.color || 'secondary'}`">
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Tip Seçimi --}}
                            <div class="col-md-3">
                                <label class="form-label">Badge Tipi</label>
                                <select class="form-select" x-model="badge.type">
                                    <option value="new_arrival">✨ Yeni Ürün</option>
                                    <option value="discount">🏷️ İndirim</option>
                                    <option value="limited_stock">⚠️ Sınırlı Stok</option>
                                    <option value="free_shipping">🚚 Ücretsiz Kargo</option>
                                    <option value="bestseller">🔥 Çok Satan</option>
                                    <option value="featured">⭐ Öne Çıkan</option>
                                    <option value="eco_friendly">🌿 Çevre Dostu</option>
                                    <option value="warranty">🛡️ Garanti</option>
                                    <option value="pre_order">⏰ Ön Sipariş</option>
                                    <option value="imported">🌍 İthal</option>
                                    <option value="custom">📌 Özel</option>
                                </select>
                            </div>

                            {{-- Renk Seçimi --}}
                            <div class="col-md-2">
                                <label class="form-label">Renk</label>
                                <select class="form-select" x-model="badge.color">
                                    <option value="green">Yeşil</option>
                                    <option value="red">Kırmızı</option>
                                    <option value="orange">Turuncu</option>
                                    <option value="blue">Mavi</option>
                                    <option value="yellow">Sarı</option>
                                    <option value="purple">Mor</option>
                                    <option value="emerald">Zümrüt</option>
                                    <option value="indigo">Lacivert</option>
                                    <option value="cyan">Turkuaz</option>
                                    <option value="gray">Gri</option>
                                </select>
                            </div>

                            {{-- Öncelik --}}
                            <div class="col-md-2">
                                <label class="form-label">Öncelik</label>
                                <input type="number" class="form-control" x-model="badge.priority" min="1" max="99">
                                <small class="text-muted">1 = En üstte</small>
                            </div>

                            {{-- Değer (İndirim %, Adet, vb) --}}
                            <div class="col-md-2" x-show="['discount', 'limited_stock', 'warranty'].includes(badge.type)">
                                <label class="form-label" x-text="badge.type === 'discount' ? 'İndirim %' : (badge.type === 'warranty' ? 'Ay' : 'Adet')"></label>
                                <input type="number" class="form-control" x-model="badge.value">
                            </div>

                            {{-- Aktif/Pasif --}}
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" x-model="badge.is_active">
                                    <label class="form-check-label">Aktif</label>
                                </div>
                            </div>

                            {{-- Aksiyon Butonları --}}
                            <div class="col-md-1 d-flex align-items-end justify-content-end">
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    @click="removeBadge(index)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            {{-- Özel Label (Custom tip için) --}}
                            <div class="col-12" x-show="badge.type === 'custom'">
                                <label class="form-label">Özel Etiket Metni (Türkçe)</label>
                                <input type="text" class="form-control" x-model="badge.label.tr" placeholder="Örn: Kampanyalı">
                            </div>

                        </div>

                        {{-- Önizleme --}}
                        <div class="mt-3">
                            <small class="text-muted">Önizleme:</small>
                            <span class="badge ms-2"
                                :class="`bg-${badge.color || 'secondary'}`"
                                x-text="getBadgeLabel(badge)">
                            </span>
                        </div>

                    </div>
                </div>
            </template>
        </div>

    </div>
</div>

@push('scripts')
<script>
function badgeManager() {
    return {
        badges: @entangle('inputs.badges') || [],

        addBadge() {
            if (!this.badges) this.badges = [];
            this.badges.push({
                type: 'new_arrival',
                label: { tr: 'Yeni', en: 'New' },
                color: 'green',
                icon: 'sparkles',
                priority: this.badges.length + 1,
                is_active: true,
                value: null
            });
        },

        removeBadge(index) {
            if (confirm('Bu badge\'i silmek istediğinize emin misiniz?')) {
                this.badges.splice(index, 1);
            }
        },

        getBadgeLabel(badge) {
            const labels = {
                'new_arrival': 'Yeni',
                'discount': `%${badge.value || '0'} İndirim`,
                'limited_stock': `Son ${badge.value || '0'} Adet`,
                'free_shipping': 'Ücretsiz Kargo',
                'bestseller': 'Çok Satan',
                'featured': 'Öne Çıkan',
                'eco_friendly': 'Çevre Dostu',
                'warranty': `${badge.value || '0'} Ay Garanti`,
                'pre_order': 'Ön Sipariş',
                'imported': 'İthal',
                'custom': badge.label?.tr || 'Özel'
            };
            return labels[badge.type] || badge.label?.tr || 'Badge';
        }
    }
}
</script>
@endpush
