@include('muzibu::admin.helper')
@extends('admin.layout')

@section('content')
<div x-data="abuseReportsApp()">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Bugün Taranan</div>
                    </div>
                    <div class="h1 mb-0" x-text="stats.total_scanned">-</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader text-success">Temiz</div>
                    </div>
                    <div class="h1 mb-0 text-success" x-text="stats.clean">-</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader text-warning">Şüpheli</div>
                    </div>
                    <div class="h1 mb-0 text-warning" x-text="stats.suspicious">-</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader text-danger">Suistimal</div>
                    </div>
                    <div class="h1 mb-0 text-danger" x-text="stats.abuse">-</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="card">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-3">
                    <select class="form-select" x-model="filters.status" @change="loadReports()">
                        <option value="all">Tüm Durumlar</option>
                        <option value="clean">Temiz</option>
                        <option value="suspicious">Şüpheli</option>
                        <option value="abuse">Suistimal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" x-model="filters.date" @change="loadReports()">
                </div>
                <div class="col-md-3">
                    <span class="text-muted" x-show="stats.last_scan">
                        <i class="fas fa-clock me-1"></i>
                        Son tarama: <span x-text="stats.last_scan"></span>
                    </span>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#scanModal">
                        <i class="fas fa-sync me-1"></i> Tarama Başlat
                    </button>
                    <button class="btn btn-outline-secondary" @click="loadReports()" :disabled="loading">
                        <i class="fas fa-refresh" :class="loading && 'fa-spin'"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>Kullanıcı</th>
                            <th>Tarama Tarihi</th>
                            <th class="text-center">Play</th>
                            <th class="text-center">Çakışma</th>
                            <th class="text-center">Skor</th>
                            <th class="text-center">Durum</th>
                            <th class="text-center">İnceleme</th>
                            <th style="width: 80px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loading">
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!loading && reports.length === 0">
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Henüz rapor bulunmuyor. Tarama başlatın.
                                </td>
                            </tr>
                        </template>
                        <template x-for="report in reports" :key="report.id">
                            <tr>
                                <td class="text-muted" x-text="report.id"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm me-2 bg-primary-lt"
                                            x-text="(report.user?.name || 'U').charAt(0).toUpperCase()"></span>
                                        <div>
                                            <div x-text="report.user?.name || 'Bilinmiyor'"></div>
                                            <div class="text-muted small" x-text="report.user?.email || '-'"></div>
                                        </div>
                                    </div>
                                </td>
                                <td x-text="formatDate(report.scan_date)"></td>
                                <td class="text-center" x-text="report.total_plays"></td>
                                <td class="text-center">
                                    <span class="badge" :class="report.overlap_count > 0 ? 'bg-warning' : 'bg-secondary'"
                                        x-text="report.overlap_count"></span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold" :class="{
                                        'text-danger': report.abuse_score >= 600,
                                        'text-warning': report.abuse_score >= 300 && report.abuse_score < 600,
                                        'text-success': report.abuse_score < 300
                                    }" x-text="formatScore(report.abuse_score)"></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge" :class="{
                                        'bg-danger': report.status === 'abuse',
                                        'bg-warning': report.status === 'suspicious',
                                        'bg-success': report.status === 'clean'
                                    }" x-text="getStatusLabel(report.status)"></span>
                                </td>
                                <td class="text-center">
                                    <template x-if="report.reviewed_at">
                                        <span class="badge bg-info" x-text="report.action_taken || 'İncelendi'"></span>
                                    </template>
                                    <template x-if="!report.reviewed_at">
                                        <span class="badge bg-secondary">Bekliyor</span>
                                    </template>
                                </td>
                                <td class="text-end">
                                    <a :href="`{{ url('/admin/muzibu/abuse-reports') }}/${report.id}`"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                <span x-text="pagination.from"></span> - <span x-text="pagination.to"></span>
                / <span x-text="pagination.total"></span> rapor
            </p>
            <ul class="pagination m-0 ms-auto">
                <li class="page-item" :class="!pagination.prev_page_url && 'disabled'">
                    <a class="page-link" href="#" @click.prevent="goToPage(pagination.current_page - 1)">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <li class="page-item" :class="!pagination.next_page_url && 'disabled'">
                    <a class="page-link" href="#" @click.prevent="goToPage(pagination.current_page + 1)">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Scan Modal -->
    <div class="modal fade" id="scanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-search me-2"></i>Tarama Başlat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Tarama Tipi Seçimi -->
                    <div class="mb-3">
                        <label class="form-label">Tarama Tipi</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="scanType" id="scanTypePreset" value="preset" x-model="scanType">
                            <label class="btn btn-outline-primary" for="scanTypePreset">Hazır Periyot</label>
                            <input type="radio" class="btn-check" name="scanType" id="scanTypeCustom" value="custom" x-model="scanType">
                            <label class="btn btn-outline-primary" for="scanTypeCustom">Tarih Aralığı</label>
                        </div>
                    </div>

                    <!-- Hazır Periyot -->
                    <div class="mb-3" x-show="scanType === 'preset'">
                        <label class="form-label">Periyot Seç</label>
                        <select class="form-select" x-model="scanPeriod">
                            <option value="3">Son 3 Gün</option>
                            <option value="7">Son 7 Gün</option>
                            <option value="14">Son 14 Gün</option>
                            <option value="30">Son 30 Gün</option>
                            <option value="60">Son 60 Gün</option>
                            <option value="90">Son 90 Gün</option>
                        </select>
                    </div>

                    <!-- Tarih Aralığı -->
                    <div x-show="scanType === 'custom'">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Başlangıç</label>
                                <input type="date" class="form-control" x-model="scanDateStart">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Bitiş</label>
                                <input type="date" class="form-control" x-model="scanDateEnd">
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Akıllı Tarama:</strong> Sadece seçilen dönemde şarkı dinleyen ve aktif aboneliği olan kullanıcılar taranır.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" @click="startScan()" :disabled="scanning">
                        <span x-show="!scanning"><i class="fas fa-play me-1"></i>Başlat</span>
                        <span x-show="scanning"><i class="fas fa-spinner fa-spin me-1"></i>Başlatılıyor...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function abuseReportsApp() {
    return {
        loading: false,
        scanning: false,
        reports: [],
        stats: {
            total_scanned: 0,
            clean: 0,
            suspicious: 0,
            abuse: 0,
            last_scan: null
        },
        filters: {
            status: 'all',
            date: ''
        },
        pagination: {
            current_page: 1,
            from: 0,
            to: 0,
            total: 0,
            prev_page_url: null,
            next_page_url: null
        },
        scanType: 'preset',
        scanPeriod: 7,
        scanDateStart: '',
        scanDateEnd: '',

        init() {
            this.loadStats();
            this.loadReports();

            // Varsayılan tarih aralığı (son 7 gün)
            const today = new Date();
            const weekAgo = new Date(today);
            weekAgo.setDate(weekAgo.getDate() - 7);
            this.scanDateEnd = today.toISOString().split('T')[0];
            this.scanDateStart = weekAgo.toISOString().split('T')[0];
        },

        async loadStats() {
            try {
                const res = await fetch('{{ route('admin.muzibu.abuse.api.stats') }}');
                this.stats = await res.json();
            } catch (e) {
                console.error('Stats yüklenemedi:', e);
            }
        },

        async loadReports(page = 1) {
            this.loading = true;
            try {
                let url = '{{ route('admin.muzibu.abuse.api.list') }}?page=' + page;
                if (this.filters.status !== 'all') url += '&status=' + this.filters.status;
                if (this.filters.date) url += '&date=' + this.filters.date;

                const res = await fetch(url);
                const data = await res.json();

                this.reports = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    from: data.from || 0,
                    to: data.to || 0,
                    total: data.total,
                    prev_page_url: data.prev_page_url,
                    next_page_url: data.next_page_url
                };
            } catch (e) {
                console.error('Raporlar yüklenemedi:', e);
            }
            this.loading = false;
        },

        goToPage(page) {
            if (page < 1) return;
            this.loadReports(page);
        },

        async startScan() {
            // Validasyon
            if (this.scanType === 'custom') {
                if (!this.scanDateStart || !this.scanDateEnd) {
                    alert('Lütfen başlangıç ve bitiş tarihi seçin.');
                    return;
                }
                if (this.scanDateStart > this.scanDateEnd) {
                    alert('Başlangıç tarihi, bitiş tarihinden sonra olamaz.');
                    return;
                }
            }

            this.scanning = true;
            try {
                // Request body hazırla
                const body = this.scanType === 'preset'
                    ? { period_days: parseInt(this.scanPeriod) }
                    : { date_start: this.scanDateStart, date_end: this.scanDateEnd };

                console.log('Scan request:', body);

                const res = await fetch('{{ route('admin.muzibu.abuse.scan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(body)
                });

                console.log('Response status:', res.status);
                const data = await res.json();
                console.log('Response data:', data);

                if (data.success) {
                    // Modal'ı kapat
                    document.querySelector('#scanModal .btn-close')?.click();

                    alert('✅ ' + data.message);

                    // Raporları yenile
                    setTimeout(() => {
                        this.loadStats();
                        this.loadReports();
                    }, 2000);
                } else {
                    alert('❌ ' + (data.message || 'Bilinmeyen hata'));
                }
            } catch (e) {
                console.error('Scan error:', e);
                alert('❌ Tarama başlatılamadı: ' + e.message);
            }
            this.scanning = false;
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('tr-TR');
        },

        formatScore(seconds) {
            if (!seconds) return '0s';
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return m > 0 ? `${m}m ${s}s` : `${s}s`;
        },

        getStatusLabel(status) {
            const labels = {
                'clean': 'Temiz',
                'suspicious': 'Şüpheli',
                'abuse': 'Suistimal'
            };
            return labels[status] || status;
        }
    };
}
</script>
@endpush
