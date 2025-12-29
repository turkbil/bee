@include('muzibu::admin.helper')
@extends('admin.layout')

@push('styles')
<style>
    .abuse-report-page { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; }
    .abuse-card { background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(71, 85, 105, 0.5); border-radius: 12px; backdrop-filter: blur(10px); }
    .abuse-card-header { border-bottom: 1px solid rgba(71, 85, 105, 0.3); }

    /* Stats */
    .stat-box { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(71, 85, 105, 0.4); border-radius: 8px; padding: 1rem; text-align: center; }
    .stat-value { font-size: 1.75rem; font-weight: 700; }
    .stat-label { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Timeline */
    .timeline-container { position: relative; padding: 1rem 0; }
    .timeline-line { position: absolute; left: 24px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, #3b82f6, #10b981, #f59e0b, #8b5cf6); }
    .timeline-item { position: relative; padding-left: 60px; padding-bottom: 1.5rem; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot { position: absolute; left: 16px; top: 4px; width: 18px; height: 18px; border-radius: 50%; border: 3px solid #1e293b; z-index: 1; }
    .timeline-dot.desktop { background: #3b82f6; box-shadow: 0 0 8px rgba(59, 130, 246, 0.5); }
    .timeline-dot.mobile { background: #10b981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.5); }
    .timeline-dot.tablet { background: #f59e0b; box-shadow: 0 0 8px rgba(245, 158, 11, 0.5); }
    .timeline-dot.tv, .timeline-dot.smart-tv { background: #8b5cf6; box-shadow: 0 0 8px rgba(139, 92, 246, 0.5); }
    .timeline-dot.other { background: #ec4899; box-shadow: 0 0 8px rgba(236, 72, 153, 0.5); }
    .timeline-dot.overlap { background: #ef4444 !important; box-shadow: 0 0 12px rgba(239, 68, 68, 0.8); animation: pulse 2s infinite; }

    .timeline-content { background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(71, 85, 105, 0.3); border-radius: 8px; padding: 0.75rem 1rem; }
    .timeline-content.desktop { border-left: 3px solid #3b82f6; }
    .timeline-content.mobile { border-left: 3px solid #10b981; }
    .timeline-content.tablet { border-left: 3px solid #f59e0b; }
    .timeline-content.tv, .timeline-content.smart-tv { border-left: 3px solid #8b5cf6; }
    .timeline-content.other { border-left: 3px solid #ec4899; }
    .timeline-content.overlap { border-color: rgba(239, 68, 68, 0.5); background: rgba(239, 68, 68, 0.1); border-left: 3px solid #ef4444; }
    .timeline-time { font-size: 0.7rem; color: #64748b; font-family: monospace; }
    .timeline-song { font-size: 0.9rem; color: #e2e8f0; font-weight: 500; }
    .timeline-device { font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; font-weight: 600; }
    .timeline-device.desktop { background: rgba(59, 130, 246, 0.3); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.5); }
    .timeline-device.mobile { background: rgba(16, 185, 129, 0.3); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.5); }
    .timeline-device.tablet { background: rgba(245, 158, 11, 0.3); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.5); }
    .timeline-device.tv, .timeline-device.smart-tv { background: rgba(139, 92, 246, 0.3); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.5); }
    .timeline-device.other { background: rgba(236, 72, 153, 0.3); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.5); }

    /* Browser Colors */
    .browser-badge { font-size: 0.6rem; padding: 1px 5px; border-radius: 3px; margin-left: 4px; font-weight: 500; }
    .browser-badge.chrome { background: rgba(66, 133, 244, 0.3); color: #60a5fa; }
    .browser-badge.firefox { background: rgba(255, 85, 0, 0.3); color: #ff8c42; }
    .browser-badge.safari { background: rgba(0, 122, 255, 0.3); color: #38bdf8; }
    .browser-badge.edge { background: rgba(0, 120, 212, 0.3); color: #06b6d4; }
    .browser-badge.opera { background: rgba(255, 30, 30, 0.3); color: #f87171; }
    .browser-badge.ie { background: rgba(0, 75, 141, 0.3); color: #93c5fd; }
    .browser-badge.other { background: rgba(148, 163, 184, 0.3); color: #cbd5e1; }

    /* Overlap Alert */
    .overlap-alert { background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05)); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 0.5rem; }
    .overlap-badge { background: #ef4444; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 600; }

    /* Day Tabs */
    .day-tab { background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(71, 85, 105, 0.3); color: #94a3b8; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
    .day-tab:hover { background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.3); }
    .day-tab.active { background: rgba(59, 130, 246, 0.2); border-color: #3b82f6; color: #60a5fa; }
    .day-tab .overlap-count { background: #ef4444; color: white; font-size: 0.65rem; padding: 1px 5px; border-radius: 10px; margin-left: 4px; }

    /* Status Badge */
    .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
    .status-badge.clean { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .status-badge.suspicious { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .status-badge.abuse { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

    @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

    /* Horizontal Timeline Bars (v5 style) */
    .gantt-container { background: rgba(15, 23, 42, 0.8); border-radius: 12px; padding: 1.5rem; overflow-x: auto; }
    .gantt-time-axis { display: flex; justify-content: space-between; padding-left: 100px; margin-bottom: 0.5rem; font-size: 0.7rem; color: #64748b; font-family: monospace; }
    .gantt-row { display: flex; align-items: center; margin-bottom: 0.75rem; min-height: 40px; }
    .gantt-label { width: 100px; flex-shrink: 0; text-align: right; padding-right: 12px; }
    .gantt-track { flex: 1; position: relative; height: 36px; background: rgba(51, 65, 85, 0.3); border-radius: 6px; }
    .gantt-bar { position: absolute; height: 100%; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; padding: 0 8px; font-size: 0.65rem; color: white; font-weight: 500; transition: all 0.2s; min-width: 60px; overflow: hidden; }
    .gantt-bar:hover { transform: scaleY(1.1); z-index: 10; }
    .gantt-bar.desktop { background: linear-gradient(135deg, #3b82f6, #2563eb); border: 1px solid rgba(96, 165, 250, 0.5); }
    .gantt-bar.mobile { background: linear-gradient(135deg, #10b981, #059669); border: 1px solid rgba(52, 211, 153, 0.5); }
    .gantt-bar.tablet { background: linear-gradient(135deg, #f59e0b, #d97706); border: 1px solid rgba(251, 191, 36, 0.5); }
    .gantt-bar.overlap { background: repeating-linear-gradient(45deg, #ef4444, #ef4444 8px, #dc2626 8px, #dc2626 16px) !important; animation: pulse-opacity 1.5s infinite; }
    @keyframes pulse-opacity { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
    .gantt-overlap-marker { position: absolute; top: 0; bottom: 0; background: rgba(239, 68, 68, 0.3); border-left: 2px solid #ef4444; border-right: 2px solid #ef4444; z-index: 5; }
    .overlap-section { border-left: 4px solid; margin-bottom: 1rem; }
    .overlap-section.severity-low { border-color: #22c55e; background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), transparent); }
    .overlap-section.severity-medium { border-color: #eab308; background: linear-gradient(135deg, rgba(234, 179, 8, 0.1), transparent); }
    .overlap-section.severity-high { border-color: #ef4444; background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), transparent); }

    /* Review Form */
    .review-card { background: rgba(30, 41, 59, 0.9); }
    .form-select, .form-control { background: rgba(15, 23, 42, 0.8) !important; border-color: rgba(71, 85, 105, 0.5) !important; color: #e2e8f0 !important; }
    .form-select:focus, .form-control:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important; }
</style>
@endpush

@section('content')
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
            @elseif($report->status === 'suspicious')
                <i class="fas fa-question-circle me-1"></i> ŞÜPHELİ
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
                <div class="stat-value text-warning">{{ $report->overlap_count }}</div>
                <div class="stat-label">Çakışma</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-box">
                <div class="stat-value text-danger">{{ floor($report->abuse_score / 60) }}m {{ $report->abuse_score % 60 }}s</div>
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

    <!-- Üst Satır: 3 Kart -->
    <div class="row g-3 mb-4">
        <!-- Rapor Bilgileri -->
        <div class="col-md-4">
            <div class="abuse-card h-100">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-info-circle me-2 text-info"></i> Rapor Bilgileri
                    </h5>
                </div>
                <div class="p-3">
                    <table class="table table-sm table-borderless text-slate-300 mb-0">
                        <tr>
                            <td class="text-slate-500">Rapor ID</td>
                            <td class="text-end">#{{ $report->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-slate-500">Kullanıcı ID</td>
                            <td class="text-end">#{{ $report->user_id }}</td>
                        </tr>
                        <tr>
                            <td class="text-slate-500">Tarama Tarihi</td>
                            <td class="text-end">{{ $report->scan_date->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-slate-500">Dönem</td>
                            <td class="text-end">{{ $report->period_start->format('d.m') }} - {{ $report->period_end->format('d.m.Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-slate-500">Oluşturulma</td>
                            <td class="text-end">{{ $report->created_at->diffForHumans() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Günlük Özet -->
        <div class="col-md-4">
            <div class="abuse-card h-100">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-calendar-alt me-2 text-warning"></i> Günlük Özet
                    </h5>
                </div>
                <div class="p-3" style="max-height: 200px; overflow-y: auto;">
                    @foreach($report->daily_stats ?? [] as $date => $stat)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: rgba(71, 85, 105, 0.3) !important;">
                        <div>
                            <div class="text-slate-300 small fw-bold">{{ \Carbon\Carbon::parse($date)->locale('tr')->isoFormat('D MMM ddd') }}</div>
                            <div class="text-slate-500" style="font-size: 0.7rem;">
                                <span class="text-primary">{{ $stat['desktop'] ?? 0 }} PC</span> &bull;
                                <span class="text-success">{{ $stat['mobile'] ?? 0 }} Mob</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-white small">{{ $stat['plays'] ?? 0 }}</div>
                            @if(($stat['overlaps'] ?? 0) > 0)
                                <div class="text-danger" style="font-size: 0.7rem;">{{ $stat['overlaps'] }} çakışma</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Admin İnceleme -->
        <div class="col-md-4">
            <div class="abuse-card review-card h-100">
                <div class="abuse-card-header p-3">
                    <h5 class="text-white mb-0">
                        <i class="fas fa-user-check me-2 text-success"></i> Admin İnceleme
                    </h5>
                </div>
                <div class="p-3">
                    @if($report->is_reviewed)
                        <div class="p-3 rounded" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3);">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="text-success fw-bold">İncelendi</span>
                            </div>
                            <div class="text-slate-400 small">
                                {{ $report->reviewer?->name ?? 'Admin' }} &bull; {{ $report->reviewed_at->diffForHumans() }}
                            </div>
                            @if($report->action_label)
                                <div class="mt-2">
                                    <span class="badge bg-primary">{{ $report->action_label }}</span>
                                </div>
                            @endif
                            @if($report->notes)
                                <div class="mt-2 text-slate-300 small">
                                    <i class="fas fa-sticky-note me-1"></i> {{ $report->notes }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mb-2">
                            <label class="form-label text-slate-400 small mb-1">Aksiyon</label>
                            <select class="form-select form-select-sm" x-model="review.action">
                                <option value="none">İşlem Yapılmadı</option>
                                <option value="warned">Uyarı Gönder</option>
                                <option value="suspended">Hesabı Askıya Al</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-slate-400 small mb-1">Notlar</label>
                            <textarea class="form-control form-control-sm" rows="2" x-model="review.notes" placeholder="İsteğe bağlı not..."></textarea>
                        </div>
                        <button class="btn btn-primary btn-sm w-100" @click="submitReview()" :disabled="submitting">
                            <span x-show="!submitting"><i class="fas fa-save me-1"></i> Kaydet</span>
                            <span x-show="submitting"><i class="fas fa-spinner fa-spin me-1"></i> Kaydediliyor...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Timeline (Full Width) -->
        <div class="col-12">
            <div class="abuse-card">
                <div class="abuse-card-header p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-white mb-0">
                            <i class="fas fa-stream me-2 text-primary"></i> Dinleme Zaman Çizelgesi
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge" style="background: rgba(59, 130, 246, 0.3); color: #60a5fa;">
                                <i class="fas fa-desktop me-1"></i> Desktop
                            </span>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.3); color: #34d399;">
                                <i class="fas fa-mobile-alt me-1"></i> Mobile
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Day Tabs -->
                <div class="p-3 border-bottom" style="border-color: rgba(71, 85, 105, 0.3) !important;">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($report->daily_stats ?? [] as $date => $dayStat)
                            <div class="day-tab" :class="selectedDate === '{{ $date }}' && 'active'" @click="selectDate('{{ $date }}')">
                                {{ \Carbon\Carbon::parse($date)->locale('tr')->isoFormat('D MMM ddd') }}
                                @if(($dayStat['overlaps'] ?? 0) > 0)
                                    <span class="overlap-count">{{ $dayStat['overlaps'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Genel Zaman Çizelgesi (v5 style) -->
                <div class="p-3" style="max-height: 1200px; overflow-y: auto;">
                    @php
                        $allItems = collect($timelineData['items'] ?? []);
                        $allOverlaps = $timelineData['overlaps'] ?? [];
                        $overlapIds = collect($allOverlaps)->flatMap(fn($o) => [$o['play1']['id'], $o['play2']['id']])->unique();

                        // Seçili güne göre filtrele
                        $selectedDateItems = $allItems;

                        // Zaman aralığını hesapla
                        if ($allItems->isNotEmpty()) {
                            $minTime = $allItems->min('start');
                            $maxTime = $allItems->max('end');
                            $minTs = strtotime($minTime);
                            $maxTs = strtotime($maxTime);
                            $totalSeconds = max($maxTs - $minTs, 1);
                        } else {
                            $minTs = $maxTs = $totalSeconds = 0;
                        }

                        // Cihaz gruplarını oluştur (device_key bazlı)
                        $deviceGroups = $allItems->groupBy('device_key');
                    @endphp

                    @if(count($allOverlaps) > 0)
                    <!-- Genel Zaman Çizelgesi Başlık -->
                    <div class="abuse-card mb-4">
                        <div class="p-3" style="border-bottom: 1px solid rgba(71, 85, 105, 0.3);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="text-white mb-0">
                                    <i class="fas fa-chart-gantt me-2 text-purple-400"></i>
                                    Genel Zaman Çizelgesi
                                </h6>
                                <span class="badge bg-danger">{{ count($allOverlaps) }} ÇAKIŞMA</span>
                            </div>
                            @if($allItems->isNotEmpty())
                            <div class="text-slate-500 small mt-1">
                                {{ \Carbon\Carbon::parse($minTime)->format('H:i') }} - {{ \Carbon\Carbon::parse($maxTime)->format('H:i') }} arası
                            </div>
                            @endif
                        </div>

                        <div class="gantt-container">
                            <!-- Zaman Ekseni -->
                            <div class="gantt-time-axis">
                                @php
                                    // 5-6 zaman noktası göster
                                    $timePoints = 6;
                                    $interval = $totalSeconds / ($timePoints - 1);
                                @endphp
                                @for($i = 0; $i < $timePoints; $i++)
                                    <span>{{ date('H:i', $minTs + ($interval * $i)) }}</span>
                                @endfor
                            </div>

                            <!-- Her cihaz için satır -->
                            @foreach($deviceGroups as $deviceKey => $devicePlays)
                            @php
                                $firstPlay = $devicePlays->first();
                                $device = $firstPlay['group'] ?? 'desktop';
                                $browser = $firstPlay['browser'] ?? 'other';
                                $platform = $firstPlay['platform'] ?? 'Unknown';
                            @endphp
                            <div class="gantt-row">
                                <div class="gantt-label">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <i class="fas fa-{{ $device === 'mobile' ? 'mobile-alt' : ($device === 'tablet' ? 'tablet-alt' : 'desktop') }} text-{{ $device === 'mobile' ? 'success' : ($device === 'tablet' ? 'warning' : 'primary') }}" style="font-size: 0.8rem;"></i>
                                        <span class="text-slate-400" style="font-size: 0.65rem;">{{ ucfirst($browser) }}</span>
                                    </div>
                                    <div class="text-slate-600" style="font-size: 0.55rem;">{{ Str::limit($platform, 10) }}</div>
                                </div>
                                <div class="gantt-track">
                                    @foreach($devicePlays as $play)
                                    @php
                                        $playStart = strtotime($play['start']);
                                        $playEnd = strtotime($play['end']);
                                        $leftPercent = (($playStart - $minTs) / $totalSeconds) * 100;
                                        $widthPercent = max((($playEnd - $playStart) / $totalSeconds) * 100, 3);
                                        $isOverlap = $overlapIds->contains($play['id']);
                                    @endphp
                                    <div class="gantt-bar {{ $device }} {{ $isOverlap ? 'overlap' : '' }}"
                                         style="left: {{ $leftPercent }}%; width: {{ $widthPercent }}%;"
                                         title="{{ $play['content'] ?? 'Şarkı' }} ({{ \Carbon\Carbon::parse($play['start'])->format('H:i:s') }} - {{ \Carbon\Carbon::parse($play['end'])->format('H:i:s') }})">
                                        <span>{{ \Carbon\Carbon::parse($play['start'])->format('H:i') }}</span>
                                        <span>{{ \Carbon\Carbon::parse($play['end'])->format('H:i') }}</span>
                                    </div>
                                    @endforeach

                                    <!-- Overlap işaretçileri -->
                                    @foreach($allOverlaps as $overlap)
                                    @php
                                        $oStart = strtotime($overlap['overlap_start']);
                                        $oEnd = strtotime($overlap['overlap_end']);
                                        $oLeftPercent = (($oStart - $minTs) / $totalSeconds) * 100;
                                        $oWidthPercent = max((($oEnd - $oStart) / $totalSeconds) * 100, 0.5);
                                    @endphp
                                    <div class="gantt-overlap-marker" style="left: {{ $oLeftPercent }}%; width: {{ $oWidthPercent }}%;"></div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Çakışma Detayları (v5 style) -->
                    <div class="text-danger small fw-bold mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ count($allOverlaps) }} ÇAKIŞMA TESPİT EDİLDİ
                    </div>

                    @foreach($allOverlaps as $idx => $overlap)
                    @php
                        $browser1 = strtolower($overlap['play1']['browser'] ?? 'unknown');
                        $browser2 = strtolower($overlap['play2']['browser'] ?? 'unknown');
                        $platform1 = $overlap['play1']['platform'] ?? 'Unknown';
                        $platform2 = $overlap['play2']['platform'] ?? 'Unknown';
                        $severity = $overlap['overlap_seconds'] >= 120 ? 'high' : ($overlap['overlap_seconds'] >= 60 ? 'medium' : 'low');
                        $severityColor = $severity === 'high' ? 'danger' : ($severity === 'medium' ? 'warning' : 'success');
                        $severityBg = $severity === 'high' ? 'red' : ($severity === 'medium' ? 'yellow' : 'green');

                        // Bu çakışmanın zaman aralığı
                        $p1Start = strtotime($overlap['play1']['start']);
                        $p1End = strtotime($overlap['play1']['end']);
                        $p2Start = strtotime($overlap['play2']['start']);
                        $p2End = strtotime($overlap['play2']['end']);
                        $overlapMinTs = min($p1Start, $p2Start);
                        $overlapMaxTs = max($p1End, $p2End);
                        $overlapTotalSec = max($overlapMaxTs - $overlapMinTs, 1);
                    @endphp
                    <div class="overlap-section severity-{{ $severity }} rounded p-3 mb-3">
                        <!-- Başlık -->
                        <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid rgba(71, 85, 105, 0.3); padding-bottom: 0.75rem;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: rgba(var(--bs-{{ $severityColor }}-rgb), 0.2);">
                                    <span class="text-{{ $severityColor }} fw-bold">{{ $idx + 1 }}</span>
                                </div>
                                <div>
                                    <div class="text-white fw-bold">Çakışma #{{ $idx + 1 }} - {{ $severity === 'high' ? 'Yüksek' : ($severity === 'medium' ? 'Orta' : 'Düşük') }} Şiddet</div>
                                    <div class="text-slate-500 small">{{ \Carbon\Carbon::parse($overlap['overlap_start'])->format('H:i:s') }} - {{ \Carbon\Carbon::parse($overlap['overlap_end'])->format('H:i:s') }} arası</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="text-{{ $severityColor }} fw-bold" style="font-size: 1.25rem;">{{ $overlap['overlap_seconds'] }} sn</div>
                                <div class="text-slate-500 small">çakışma süresi</div>
                            </div>
                        </div>

                        <!-- Horizontal Timeline Bars -->
                        <div class="p-3 rounded mb-3" style="background: rgba(15, 23, 42, 0.6);">
                            <!-- Play 1 -->
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div style="width: 80px;" class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <i class="fas fa-{{ $overlap['play1']['device'] === 'mobile' ? 'mobile-alt' : 'desktop' }} text-{{ $overlap['play1']['device'] === 'mobile' ? 'success' : 'primary' }}"></i>
                                        <span class="text-{{ $overlap['play1']['device'] === 'mobile' ? 'success' : 'primary' }}" style="font-size: 0.7rem;">{{ ucfirst($overlap['play1']['device']) }}</span>
                                    </div>
                                    <div class="text-slate-600" style="font-size: 0.6rem;">#{{ $overlap['play1']['id'] }}</div>
                                </div>
                                <div class="flex-grow-1 position-relative" style="height: 36px;">
                                    @php
                                        $bar1Left = (($p1Start - $overlapMinTs) / $overlapTotalSec) * 100;
                                        $bar1Width = max((($p1End - $p1Start) / $overlapTotalSec) * 100, 20);
                                    @endphp
                                    <div class="gantt-bar {{ $overlap['play1']['device'] }}"
                                         style="left: {{ $bar1Left }}%; width: {{ $bar1Width }}%;">
                                        <span>{{ \Carbon\Carbon::parse($overlap['play1']['start'])->format('H:i:s') }}</span>
                                        <span>{{ \Carbon\Carbon::parse($overlap['play1']['end'])->format('H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Play 2 -->
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div style="width: 80px;" class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <i class="fas fa-{{ $overlap['play2']['device'] === 'mobile' ? 'mobile-alt' : 'desktop' }} text-{{ $overlap['play2']['device'] === 'mobile' ? 'success' : 'primary' }}"></i>
                                        <span class="text-{{ $overlap['play2']['device'] === 'mobile' ? 'success' : 'primary' }}" style="font-size: 0.7rem;">{{ ucfirst($overlap['play2']['device']) }}</span>
                                    </div>
                                    <div class="text-slate-600" style="font-size: 0.6rem;">#{{ $overlap['play2']['id'] }}</div>
                                </div>
                                <div class="flex-grow-1 position-relative" style="height: 36px;">
                                    @php
                                        $bar2Left = (($p2Start - $overlapMinTs) / $overlapTotalSec) * 100;
                                        $bar2Width = max((($p2End - $p2Start) / $overlapTotalSec) * 100, 20);
                                    @endphp
                                    <div class="gantt-bar {{ $overlap['play2']['device'] }}"
                                         style="left: {{ $bar2Left }}%; width: {{ $bar2Width }}%;">
                                        <span>{{ \Carbon\Carbon::parse($overlap['play2']['start'])->format('H:i:s') }}</span>
                                        <span>{{ \Carbon\Carbon::parse($overlap['play2']['end'])->format('H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Çakışma göstergesi -->
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 80px;" class="text-end">
                                    <i class="fas fa-exclamation-triangle text-{{ $severityColor }}"></i>
                                </div>
                                <div class="flex-grow-1 text-center">
                                    <span class="px-3 py-1 rounded-pill text-{{ $severityColor }}" style="background: rgba(var(--bs-{{ $severityColor }}-rgb), 0.15); border: 1px solid rgba(var(--bs-{{ $severityColor }}-rgb), 0.3); font-size: 0.75rem;">
                                        <i class="fas fa-clock me-1"></i>{{ $overlap['overlap_seconds'] }} saniye çakışma
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Cihaz Detayları -->
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: rgba({{ $overlap['play1']['device'] === 'mobile' ? '16, 185, 129' : '59, 130, 246' }}, 0.1); border: 1px solid rgba({{ $overlap['play1']['device'] === 'mobile' ? '16, 185, 129' : '59, 130, 246' }}, 0.3);">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-{{ $overlap['play1']['device'] === 'mobile' ? 'mobile-alt' : 'desktop' }} text-{{ $overlap['play1']['device'] === 'mobile' ? 'success' : 'primary' }}"></i>
                                        <span class="fw-medium text-{{ $overlap['play1']['device'] === 'mobile' ? 'success' : 'primary' }}">{{ ucfirst($platform1) }} {{ ucfirst($browser1) }}</span>
                                    </div>
                                    <div class="text-white small fw-bold mb-1">
                                        <i class="fas fa-music me-1 text-slate-500"></i>
                                        {{ Str::limit($overlap['play1']['song'] ?? '', 30) }}
                                    </div>
                                    <div class="text-slate-500" style="font-size: 0.65rem;">
                                        Play #{{ $overlap['play1']['id'] }} • IP: {{ Str::limit($overlap['play1']['ip'] ?? '', 15) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background: rgba({{ $overlap['play2']['device'] === 'mobile' ? '16, 185, 129' : '59, 130, 246' }}, 0.1); border: 1px solid rgba({{ $overlap['play2']['device'] === 'mobile' ? '16, 185, 129' : '59, 130, 246' }}, 0.3);">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-{{ $overlap['play2']['device'] === 'mobile' ? 'mobile-alt' : 'desktop' }} text-{{ $overlap['play2']['device'] === 'mobile' ? 'success' : 'primary' }}"></i>
                                        <span class="fw-medium text-{{ $overlap['play2']['device'] === 'mobile' ? 'success' : 'primary' }}">{{ ucfirst($platform2) }} {{ ucfirst($browser2) }}</span>
                                    </div>
                                    <div class="text-white small fw-bold mb-1">
                                        <i class="fas fa-music me-1 text-slate-500"></i>
                                        {{ Str::limit($overlap['play2']['song'] ?? '', 30) }}
                                    </div>
                                    <div class="text-slate-500" style="font-size: 0.65rem;">
                                        Play #{{ $overlap['play2']['id'] }} • IP: {{ Str::limit($overlap['play2']['ip'] ?? '', 15) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Uyarı Bilgileri -->
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @if($overlap['same_browser'] ?? false)
                            <div class="p-2 rounded text-danger small flex-grow-1" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);">
                                <i class="fas fa-clone me-1"></i>
                                <strong>Aynı Tarayıcı!</strong>
                                <span class="text-slate-400 ms-1">(2 sekme açılmış olabilir)</span>
                            </div>
                            @endif
                            @if($overlap['same_ip'] ?? ($overlap['play1']['ip'] === $overlap['play2']['ip']))
                            <div class="p-2 rounded text-warning small flex-grow-1" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3);">
                                <i class="fas fa-network-wired me-1"></i>
                                Aynı IP: <span class="font-monospace">{{ $overlap['play1']['ip'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endif

                    <!-- Tüm Dinlemeler Listesi -->
                    <div class="mt-4 pt-3" style="border-top: 1px solid rgba(71, 85, 105, 0.3);">
                        <h6 class="text-slate-400 mb-3">
                            <i class="fas fa-list me-2"></i>
                            Tüm Dinlemeler ({{ count($timelineData['items'] ?? []) }} kayıt)
                        </h6>
                    </div>

                    <div class="timeline-container">
                        <div class="timeline-line"></div>
                        @php
                            $items = collect($timelineData['items'] ?? [])->sortBy('start');
                            $overlapIds = collect($timelineData['overlaps'] ?? [])->flatMap(fn($o) => [$o['play1']['id'], $o['play2']['id']])->unique();
                        @endphp

                        @forelse($items as $item)
                            @php
                                $isOverlap = $overlapIds->contains($item['id']);
                                $browser = strtolower($item['browser'] ?? 'other');
                                $platform = $item['platform'] ?? 'Unknown';
                                $deviceIcon = match($item['group']) {
                                    'mobile' => 'mobile-alt',
                                    'tablet' => 'tablet-alt',
                                    'tv', 'smart-tv' => 'tv',
                                    default => 'desktop'
                                };
                                $browserIcon = match($browser) {
                                    'chrome' => 'fab fa-chrome',
                                    'firefox' => 'fab fa-firefox',
                                    'safari' => 'fab fa-safari',
                                    'edge' => 'fab fa-edge',
                                    'opera' => 'fab fa-opera',
                                    'ie' => 'fab fa-internet-explorer',
                                    default => 'fas fa-globe'
                                };
                                $platformIcon = match(true) {
                                    str_contains(strtolower($platform), 'mac') || str_contains(strtolower($platform), 'os x') => 'fab fa-apple',
                                    str_contains(strtolower($platform), 'windows') => 'fab fa-windows',
                                    str_contains(strtolower($platform), 'linux') => 'fab fa-linux',
                                    str_contains(strtolower($platform), 'android') => 'fab fa-android',
                                    str_contains(strtolower($platform), 'ios') => 'fab fa-apple',
                                    default => 'fas fa-laptop'
                                };
                            @endphp
                            <div class="timeline-item" x-show="!selectedDate || '{{ \Carbon\Carbon::parse($item['start'])->format('Y-m-d') }}' === selectedDate">
                                <div class="timeline-dot {{ $item['group'] }} {{ $isOverlap ? 'overlap' : '' }}"></div>
                                <div class="timeline-content {{ $item['group'] }} {{ $isOverlap ? 'overlap' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex flex-wrap align-items-center gap-1">
                                            <span class="timeline-device {{ $item['group'] }}">
                                                <i class="fas fa-{{ $deviceIcon }} me-1"></i>
                                                {{ ucfirst($item['group']) }}
                                            </span>
                                            <span class="browser-badge {{ $browser }}">
                                                <i class="{{ $browserIcon }} me-1"></i>{{ ucfirst($browser) }}
                                            </span>
                                            <span class="text-slate-500" style="font-size: 0.6rem;">
                                                <i class="{{ $platformIcon }} me-1"></i>{{ $platform }}
                                            </span>
                                            @if($isOverlap)
                                                <span class="overlap-badge ms-1">ÇAKIŞMA</span>
                                            @endif
                                        </div>
                                        <div class="timeline-time text-nowrap">
                                            {{ \Carbon\Carbon::parse($item['start'])->format('H:i:s') }}
                                            <i class="fas fa-arrow-right mx-1"></i>
                                            {{ \Carbon\Carbon::parse($item['end'])->format('H:i:s') }}
                                        </div>
                                    </div>
                                    <div class="timeline-song mt-1">
                                        <i class="fas fa-music me-1 text-slate-500"></i>
                                        {{ $item['content'] ?? 'Bilinmeyen Şarkı' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-slate-500 py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <div>Bu dönemde dinleme kaydı yok</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const dailyStatsData = @json($report->daily_stats ?? []);

function reportDetailApp() {
    return {
        selectedDate: null,
        review: { action: 'none', notes: '' },
        submitting: false,

        init() {
            const dates = Object.keys(dailyStatsData);
            if (dates.length > 0) {
                // Çakışma olan ilk günü seç, yoksa son günü
                const daysWithOverlaps = dates.filter(d => (dailyStatsData[d]?.overlaps || 0) > 0);
                this.selectedDate = daysWithOverlaps.length > 0 ? daysWithOverlaps[0] : dates[dates.length - 1];
            }
        },

        selectDate(date) {
            this.selectedDate = this.selectedDate === date ? null : date;
        },

        async submitReview() {
            this.submitting = true;
            try {
                const res = await fetch('{{ route("admin.muzibu.abuse.review", $report->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.review)
                });
                const data = await res.json();
                if (data.success) {
                    alert('Kaydedildi!');
                    location.reload();
                } else {
                    alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
                }
            } catch (e) {
                alert('Hata: ' + e.message);
            }
            this.submitting = false;
        }
    };
}
</script>
@endpush
