@include('muzibu::admin.helper')
@extends('admin.layout')

@push('styles')
<script src="https://unpkg.com/vis-timeline@7.7.3/standalone/umd/vis-timeline-graph2d.min.js"></script>
<link href="https://unpkg.com/vis-timeline@7.7.3/styles/vis-timeline-graph2d.min.css" rel="stylesheet">
<style>
    .abuse-report-page { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; }
    .abuse-card { background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(71, 85, 105, 0.5); border-radius: 12px; }
    .abuse-card-header { border-bottom: 1px solid rgba(71, 85, 105, 0.3); }
    .stat-box { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(71, 85, 105, 0.4); border-radius: 8px; padding: 1rem; text-align: center; }
    .stat-value { font-size: 1.75rem; font-weight: 700; }
    .stat-label { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; }
    .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
    .status-badge.clean { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .status-badge.abuse { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
    .pattern-card { background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem; }
    .pattern-card.detected { border-left: 4px solid #ef4444; }
    .pattern-card.clean { border-left: 4px solid #22c55e; opacity: 0.6; }
    .day-tab { background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(71, 85, 105, 0.3); color: #94a3b8; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; }
    .day-tab:hover { background: rgba(59, 130, 246, 0.1); }
    .day-tab.active { background: rgba(59, 130, 246, 0.2); border-color: #3b82f6; color: #60a5fa; }
    .overlap-item { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem; }
    .vis-timeline { border: none !important; background: transparent !important; }
    .vis-item { border-radius: 4px; font-size: 10px; }
    .vis-item.overlap { background: #ef4444 !important; border-color: #f87171 !important; }
    .vis-label { color: #e2e8f0 !important; font-size: 11px; }
    .vis-labelset .vis-label { background: rgba(15, 23, 42, 0.95) !important; }
    .vis-time-axis .vis-text { color: #94a3b8 !important; font-size: 10px; }
    .form-select, .form-control { background: rgba(15, 23, 42, 0.8) !important; border-color: rgba(71, 85, 105, 0.5) !important; color: #e2e8f0 !important; }
</style>
@endpush

@section('content')
@php
    // Controller'dan gelen limitli veri varsa onu kullan (performans)
    $patterns = $limitedPatterns ?? $report->patterns_json ?? [];
    $isEarlyExit = $patterns['early_exit'] ?? false;
    $pingPong = $patterns['ping_pong'] ?? ['detected' => false];
    $concurrent = $patterns['concurrent_different'] ?? ['detected' => false];
    $splitStream = $patterns['split_stream'] ?? ['detected' => false];

    // Overlap samples (max 50)
    $overlapSamples = array_slice($splitStream['samples'] ?? [], 0, 50);
    $totalOverlaps = $splitStream['count'] ?? 0;
@endphp

<div class="abuse-report-page p-4" x-data="reportDetailApp()">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.muzibu.abuse.index') }}" class="btn btn-sm btn-outline-light mb-2">
                <i class="fas fa-arrow-left me-1"></i> Geri
            </a>
            <h2 class="text-white mb-0">
                <i class="fas fa-user-shield me-2 text-primary"></i>
                Abuse Raporu #{{ $report->id }}
            </h2>
            <p class="text-slate-400 mb-0">{{ $report->user?->name ?? 'Bilinmiyor' }} &bull; {{ $report->user?->email ?? '-' }}</p>
        </div>
        <div class="status-badge {{ $report->status }}">
            @if($report->status === 'abuse')
                <i class="fas fa-exclamation-circle me-1"></i> SUİSTİMAL
            @else
                <i class="fas fa-check-circle me-1"></i> TEMİZ
            @endif
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="stat-box">
                <div class="stat-value text-white">{{ $report->total_plays }}</div>
                <div class="stat-label">Toplam Play</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box">
                <div class="stat-value text-primary">{{ $timelineData['stats']['desktop_plays'] ?? 0 }}</div>
                <div class="stat-label"><i class="fas fa-desktop me-1"></i> Desktop</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box">
                <div class="stat-value text-success">{{ $timelineData['stats']['mobile_plays'] ?? 0 }}</div>
                <div class="stat-label"><i class="fas fa-mobile-alt me-1"></i> Mobile</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box">
                <div class="stat-value text-warning">{{ $totalOverlaps }}</div>
                <div class="stat-label">Overlap</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box">
                <div class="stat-value text-danger">{{ $report->abuse_score }}</div>
                <div class="stat-label">Abuse Score</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box">
                <div class="stat-value text-info">{{ $report->period_start->format('d.m') }} - {{ $report->period_end->format('d.m') }}</div>
                <div class="stat-label">Dönem</div>
            </div>
        </div>
    </div>

    <!-- Pattern Analizi (Yeni 3-Pattern Sistemi) -->
    <div class="abuse-card mb-4">
        <div class="abuse-card-header p-3">
            <h5 class="text-white mb-0">
                <i class="fas fa-fingerprint me-2 text-purple-400"></i>
                Pattern Analizi (Ping-Pong v2.0)
            </h5>
        </div>
        <div class="p-3">
            @if($isEarlyExit)
                <div class="alert alert-success d-flex align-items-center" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-bolt fa-2x text-success me-3"></i>
                    <div>
                        <strong class="text-success">⚡ Early Exit</strong>
                        <div class="text-slate-400 small">Tek fingerprint tespit edildi. Ping-pong olamaz → Otomatik TEMİZ</div>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    <!-- Ping-Pong -->
                    <div class="col-md-4">
                        <div class="pattern-card {{ $pingPong['detected'] ? 'detected' : 'clean' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fw-bold"><i class="fas fa-sync-alt me-2"></i>Ping-Pong</span>
                                @if($pingPong['detected'])
                                    <span class="badge bg-danger">TESPİT</span>
                                @else
                                    <span class="badge bg-success">TEMİZ</span>
                                @endif
                            </div>
                            <p class="text-slate-400 small mb-2">A→B→A döngüsü (IP, browser, platform)</p>
                            @if($pingPong['detected'])
                                <div class="text-danger small">
                                    <strong>{{ count($pingPong['cycles'] ?? []) }}</strong> döngü tespit edildi
                                    <br>Alanlar: {{ implode(', ', $pingPong['fields'] ?? []) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Concurrent Different -->
                    <div class="col-md-4">
                        <div class="pattern-card {{ $concurrent['detected'] ? 'detected' : 'clean' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fw-bold"><i class="fas fa-code-branch me-2"></i>Concurrent</span>
                                @if($concurrent['detected'])
                                    <span class="badge bg-danger">TESPİT</span>
                                @else
                                    <span class="badge bg-success">TEMİZ</span>
                                @endif
                            </div>
                            <p class="text-slate-400 small mb-2">Aynı anda farklı lokasyon/cihaz</p>
                            @if($concurrent['detected'])
                                <div class="text-danger small">
                                    <strong>{{ $concurrent['count'] ?? 0 }}</strong> eş zamanlı farklı kaynak
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Split Stream -->
                    <div class="col-md-4">
                        <div class="pattern-card {{ $splitStream['detected'] ? 'detected' : 'clean' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fw-bold"><i class="fas fa-tv me-2"></i>Split Stream</span>
                                @if($splitStream['detected'])
                                    <span class="badge bg-danger">TESPİT</span>
                                @else
                                    <span class="badge bg-success">TEMİZ</span>
                                @endif
                            </div>
                            <p class="text-slate-400 small mb-2">1 PC → 2 hoparlör (aynı fingerprint + overlap)</p>
                            @if($splitStream['detected'])
                                <div class="text-danger small">
                                    <strong>{{ $splitStream['count'] ?? 0 }}</strong> split stream overlap
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Sol Kolon: Bilgiler + İnceleme -->
        <div class="col-md-4">
            <!-- Rapor Bilgileri -->
            <div class="abuse-card mb-4">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0"><i class="fas fa-info-circle me-2 text-info"></i> Bilgiler</h5>
                </div>
                <div class="p-3">
                    <table class="table table-sm table-borderless text-slate-300 mb-0">
                        <tr><td class="text-slate-500">Rapor ID</td><td class="text-end">#{{ $report->id }}</td></tr>
                        <tr><td class="text-slate-500">Kullanıcı</td><td class="text-end">#{{ $report->user_id }}</td></tr>
                        <tr><td class="text-slate-500">Tarama</td><td class="text-end">{{ $report->scan_date->format('d.m.Y') }}</td></tr>
                        <tr><td class="text-slate-500">Dönem</td><td class="text-end">{{ $report->period_start->format('d.m') }} - {{ $report->period_end->format('d.m') }}</td></tr>
                    </table>
                </div>
            </div>

            <!-- Günlük Özet -->
            <div class="abuse-card mb-4">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0"><i class="fas fa-calendar-alt me-2 text-warning"></i> Günlük</h5>
                </div>
                <div class="p-3" style="max-height: 200px; overflow-y: auto;">
                    @foreach($report->daily_stats ?? [] as $date => $stat)
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(71, 85, 105, 0.3) !important;">
                        <div class="text-slate-300 small">{{ \Carbon\Carbon::parse($date)->format('d M D') }}</div>
                        <div class="text-end">
                            <span class="text-white">{{ $stat['plays'] ?? 0 }}</span>
                            @if(($stat['split_stream'] ?? 0) > 0)
                                <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">{{ $stat['split_stream'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Admin İnceleme -->
            <div class="abuse-card">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0"><i class="fas fa-user-check me-2 text-success"></i> İnceleme</h5>
                </div>
                <div class="p-3">
                    @if($report->is_reviewed)
                        <div class="text-success"><i class="fas fa-check-circle me-1"></i> İncelendi</div>
                        <div class="text-slate-400 small">{{ $report->reviewer?->name }} • {{ $report->reviewed_at->diffForHumans() }}</div>
                    @else
                        <div class="mb-2">
                            <select class="form-select form-select-sm" x-model="review.action">
                                <option value="none">İşlem Yok</option>
                                <option value="warned">Uyarı</option>
                                <option value="suspended">Askıya Al</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <textarea class="form-control form-control-sm" rows="2" x-model="review.notes" placeholder="Not..."></textarea>
                        </div>
                        <button class="btn btn-primary btn-sm w-100" @click="submitReview()" :disabled="submitting">
                            <i class="fas fa-save me-1"></i> Kaydet
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sağ Kolon: Timeline + Overlaps -->
        <div class="col-md-8">
            <!-- Timeline (vis.js) -->
            @if(count($timelineData['items'] ?? []) > 0)
            <div class="abuse-card mb-4">
                <div class="abuse-card-header p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-white mb-0"><i class="fas fa-stream me-2 text-primary"></i> Timeline</h5>
                        <div class="d-flex gap-2">
                            <span class="badge" style="background: rgba(59, 130, 246, 0.3);">Desktop</span>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.3);">Mobile</span>
                        </div>
                    </div>
                </div>
                <!-- Day Tabs -->
                <div class="p-2 border-bottom d-flex gap-2 flex-wrap" style="border-color: rgba(71, 85, 105, 0.3) !important;">
                    <div class="day-tab" :class="!selectedDate && 'active'" @click="selectDate(null)">
                        <i class="fas fa-calendar-week"></i> Tümü
                    </div>
                    @foreach($report->daily_stats ?? [] as $date => $dayStat)
                        <div class="day-tab" :class="selectedDate === '{{ $date }}' && 'active'" @click="selectDate('{{ $date }}')">
                            {{ \Carbon\Carbon::parse($date)->format('d M') }}
                        </div>
                    @endforeach
                </div>
                <div class="p-3">
                    <div id="visTimeline" style="height: 250px;"></div>
                </div>
            </div>
            @endif

            <!-- Split Stream Örnekleri -->
            @if($splitStream['detected'] && count($overlapSamples) > 0)
            <div class="abuse-card">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Split Stream Örnekleri
                        <span class="badge bg-danger ms-2">{{ $totalOverlaps }}</span>
                    </h5>
                </div>
                <div class="p-3" style="max-height: 400px; overflow-y: auto;">
                    @foreach(array_slice($overlapSamples, 0, 30) as $idx => $overlap)
                    <div class="overlap-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-white small">#{{ $idx + 1 }}</span>
                            <span class="badge bg-danger" style="font-size: 0.7rem;">{{ $overlap['overlap_seconds'] ?? 0 }}sn</span>
                        </div>
                        <div class="text-slate-400 small">
                            <i class="fas fa-music me-1"></i>{{ Str::limit($overlap['play1']['song'] ?? '', 30) }}
                            <span class="text-slate-600 ms-2">{{ \Carbon\Carbon::parse($overlap['play1']['start'])->format('H:i') }}</span>
                        </div>
                        <div class="text-slate-400 small">
                            <i class="fas fa-music me-1"></i>{{ Str::limit($overlap['play2']['song'] ?? '', 30) }}
                            <span class="text-slate-600 ms-2">{{ \Carbon\Carbon::parse($overlap['play2']['start'])->format('H:i') }}</span>
                        </div>
                    </div>
                    @endforeach
                    @if($totalOverlaps > 30)
                    <div class="text-center text-slate-500 small mt-2">
                        +{{ $totalOverlaps - 30 }} daha...
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Concurrent Different Örnekleri -->
            @if($concurrent['detected'] && count($concurrent['samples'] ?? []) > 0)
            <div class="abuse-card mt-3">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-code-branch me-2" style="color: #f59e0b;"></i>
                        Concurrent Different
                        <span class="badge bg-warning text-dark ms-2">{{ $concurrent['count'] ?? count($concurrent['samples']) }}</span>
                    </h5>
                </div>
                <div class="p-3" style="max-height: 250px; overflow-y: auto;">
                    @foreach(array_slice($concurrent['samples'] ?? [], 0, 15) as $idx => $sample)
                    <div class="overlap-item mb-2" style="border-color: rgba(245, 158, 11, 0.3); background: rgba(245, 158, 11, 0.1); padding: 0.75rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-slate-400 small">#{{ $idx + 1 }}</span>
                            <span class="text-warning small">Farklı Kaynak</span>
                        </div>
                        <div class="text-slate-300 small mt-1" style="font-size: 0.7rem;">
                            {{ Str::limit($sample['play1']['fingerprint'] ?? '', 35) }}
                        </div>
                        <div class="text-slate-300 small" style="font-size: 0.7rem;">
                            {{ Str::limit($sample['play2']['fingerprint'] ?? '', 35) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Ping-Pong Döngüleri -->
            @if($pingPong['detected'] && count($pingPong['cycles'] ?? []) > 0)
            <div class="abuse-card mt-3">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-sync-alt me-2" style="color: #a855f7;"></i>
                        Ping-Pong Döngüleri
                        <span class="badge ms-2" style="background: #a855f7;">{{ count($pingPong['cycles']) }}</span>
                    </h5>
                </div>
                <div class="p-3" style="max-height: 250px; overflow-y: auto;">
                    @foreach(array_slice($pingPong['cycles'] ?? [], 0, 10) as $idx => $cycle)
                    <div class="overlap-item mb-2" style="border-color: rgba(139, 92, 246, 0.3); background: rgba(139, 92, 246, 0.1); padding: 0.75rem;">
                        <div class="d-flex justify-content-between">
                            <span class="text-slate-400 small">#{{ $idx + 1 }} - {{ $cycle['field'] }}</span>
                            <span class="small" style="color: #a855f7;">A→B→A</span>
                        </div>
                        <div class="text-slate-300 small mt-1" style="font-size: 0.7rem; font-family: monospace;">
                            {{ Str::limit(implode(' → ', $cycle['sequence'] ?? []), 60) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dailyStatsData = @json($report->daily_stats ?? []);
const timelineItems = @json(array_slice($timelineData['items'] ?? [], 0, 500)); // Max 500 item

const browserColors = {
    'chrome': '#4285f4', 'safari': '#00d4ff', 'firefox': '#ff7139',
    'edge': '#0078d7', 'opera': '#ff1b2d', 'other': '#9ca3af'
};

function parseDate(dateStr) {
    if (!dateStr) return new Date();
    return dateStr.includes('T') ? new Date(dateStr) : new Date(dateStr.replace(' ', 'T') + '+03:00');
}

let visTimeline = null, visItems = null, allParsedItems = [];

if (document.getElementById('visTimeline') && timelineItems.length > 0) {
    const browsers = [...new Set(timelineItems.map(p => (p.browser || 'other').toLowerCase()))];
    const visGroups = new vis.DataSet(browsers.map(b => ({
        id: b,
        content: b.charAt(0).toUpperCase() + b.slice(1),
        style: 'min-height:35px;'
    })));

    allParsedItems = timelineItems.map(p => {
        const browser = (p.browser || 'other').toLowerCase();
        const isOverlap = p.className?.includes('overlap');
        const bgColor = isOverlap ? '#ef4444' : (browserColors[browser] || '#9ca3af');
        const startTime = parseDate(p.start);

        return {
            id: p.id,
            group: browser,
            content: (p.content || '').substring(0, 15),
            start: startTime,
            end: parseDate(p.end),
            dateStr: startTime.toISOString().split('T')[0],
            style: `background:${bgColor}; border-color:${bgColor}; color:#fff;`,
            className: isOverlap ? 'overlap' : ''
        };
    });

    visItems = new vis.DataSet(allParsedItems);

    visTimeline = new vis.Timeline(document.getElementById('visTimeline'), visItems, visGroups, {
        stack: true,
        zoomMin: 1000 * 60 * 5,
        zoomMax: 1000 * 60 * 60 * 48,
        orientation: { axis: 'top' },
        margin: { item: { horizontal: 2, vertical: 3 } }
    });
    visTimeline.fit({ animation: false });
}

function filterTimelineByDate(selectedDate) {
    if (!visTimeline || !visItems) return;
    if (!selectedDate) {
        visItems.clear();
        visItems.add(allParsedItems);
        visTimeline.fit({ animation: true });
        return;
    }
    const filtered = allParsedItems.filter(item => item.dateStr === selectedDate);
    visItems.clear();
    visItems.add(filtered);
    setTimeout(() => visTimeline.fit({ animation: true }), 100);
}

function reportDetailApp() {
    return {
        selectedDate: null,
        review: { action: 'none', notes: '' },
        submitting: false,

        selectDate(date) {
            this.selectedDate = this.selectedDate === date ? null : date;
            filterTimelineByDate(this.selectedDate);
        },

        async submitReview() {
            this.submitting = true;
            try {
                const res = await fetch('{{ route("admin.muzibu.abuse.review", $report->id) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.review)
                });
                const data = await res.json();
                if (data.success) location.reload();
                else alert('Hata: ' + (data.message || ''));
            } catch (e) { alert('Hata: ' + e.message); }
            this.submitting = false;
        }
    };
}
</script>
@endpush
