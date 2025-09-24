@extends('admin.layout')

@section('title', 'Kredi Hesaplayıcı')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.ai.credit-rates.index') }}">Model Credit Rates</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kredi Hesaplayıcı</li>
                    </ol>
                </nav>
                <h2 class="page-title">
                    <i class="fas fa-calculator me-2"></i>
                    Kredi Hesaplayıcı
                </h2>
                <p class="text-secondary mt-1">
                    Farklı modeller arasında kredi maliyetlerini karşılaştırın ve optimize edin
                </p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('admin.ai.credit-rates.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Geri
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <div class="row">
            <!-- Hesaplama Formu -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog me-2"></i>
                            Kredi Hesaplaması
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="credit-calculator-form">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Input Token Sayısı</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="input-tokens" value="1000" min="0">
                                        <span class="input-group-text">token</span>
                                    </div>
                                    <small class="text-secondary">Gönderilecek token miktarı</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Output Token Sayısı</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="output-tokens" value="1000" min="0">
                                        <span class="input-group-text">token</span>
                                    </div>
                                    <small class="text-secondary">Dönen token miktarı</small>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Provider'ları Seç</label>
                                    <div class="row">
                                        @foreach($providers as $provider)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <label class="form-check">
                                                <input class="form-check-input provider-checkbox" type="checkbox" 
                                                       value="{{ $provider->id }}" checked>
                                                <span class="form-check-label">
                                                    {{ $provider->name }}
                                                    <small class="text-secondary d-block">
                                                        {{ count($provider->available_models ?? []) }} model
                                                    </small>
                                                </span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="calculate-btn">
                                    <i class="fas fa-calculator me-1"></i>
                                    Hesapla
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="reset-btn">
                                    <i class="fas fa-redo me-1"></i>
                                    Sıfırla
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Anlık Sonuçlar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar me-2"></i>
                            Anlık Sonuçlar
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="quick-results">
                            <div class="text-center text-secondary py-4">
                                <i class="fas fa-calculator fa-3x mb-3"></i>
                                <p>Hesaplama için parametreleri girin ve "Hesapla" butonuna basın</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lightbulb me-2"></i>
                            Öneriler
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="recommendations">
                            <div class="text-secondary">
                                <small>Hesaplama sonrası öneriler burada görünecek</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Karşılaştırma Tablosu -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table me-2"></i>
                            Model Karşılaştırma
                        </h3>
                        <div class="card-actions">
                            <button class="btn btn-outline-success btn-sm" id="export-results">
                                <i class="fas fa-download me-1"></i>
                                Sonuçları İndir
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="comparison-table">
                            <div class="text-center text-secondary py-5">
                                <i class="fas fa-table fa-3x mb-3"></i>
                                <p>Hesaplama yapıldıktan sonra karşılaştırma tablosu burada görünecek</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Grafik Analizi -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie me-2"></i>
                            Maliyet Dağılımı
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="cost-distribution-chart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>
                            Token Verimliliği
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="efficiency-chart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
.form-check-input:checked {
    background-color: var(--tblr-primary);
    border-color: var(--tblr-primary);
}

.comparison-table .table-hover tbody tr:hover {
    background-color: rgba(var(--tblr-primary-rgb), 0.05);
}

.cost-badge {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--tblr-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let costChart = null;
    let efficiencyChart = null;
    
    // Calculator form handler
    document.getElementById('calculate-btn').addEventListener('click', function() {
        calculateCredits();
    });
    
    // Reset form handler
    document.getElementById('reset-btn').addEventListener('click', function() {
        resetForm();
    });
    
    // Export results handler
    document.getElementById('export-results').addEventListener('click', function() {
        exportResults();
    });
    
    // Real-time calculation on input change
    document.getElementById('input-tokens').addEventListener('input', debounce(calculateCredits, 500));
    document.getElementById('output-tokens').addEventListener('input', debounce(calculateCredits, 500));
    
    async function calculateCredits() {
        const inputTokens = parseInt(document.getElementById('input-tokens').value) || 0;
        const outputTokens = parseInt(document.getElementById('output-tokens').value) || 0;
        const selectedProviders = Array.from(document.querySelectorAll('.provider-checkbox:checked')).map(cb => cb.value);
        
        if (selectedProviders.length === 0) {
            showError('En az bir provider seçmelisiniz');
            return;
        }
        
        showLoading();
        
        try {
            const response = await fetch('{{ route("admin.ai.credit-rates.compare.models") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    input_tokens: inputTokens,
                    output_tokens: outputTokens,
                    provider_ids: selectedProviders
                })
            });
            
            if (!response.ok) {
                throw new Error('API request failed');
            }
            
            const data = await response.json();
            displayResults(data.data);
            
        } catch (error) {
            console.error('Error calculating credits:', error);
            showError('Hesaplama sırasında bir hata oluştu: ' + error.message);
        }
    }
    
    function displayResults(data) {
        displayQuickResults(data);
        displayComparisonTable(data);
        displayRecommendations(data);
        updateCharts(data);
    }
    
    function displayQuickResults(data) {
        const cheapest = data.cheapest_option;
        if (!cheapest) {
            document.getElementById('quick-results').innerHTML = '<div class="text-secondary">Sonuç bulunamadı</div>';
            return;
        }
        
        const html = `
            <div class="text-center">
                <div class="mb-3">
                    <span class="badge bg-green fs-3 p-3">
                        <i class="fas fa-trophy me-2"></i>
                        En Ekonomik
                    </span>
                </div>
                <h3 class="text-success">${cheapest.total_credits} Kredi</h3>
                <p class="text-secondary mb-0">Provider ID: ${cheapest.provider_id}</p>
                <p class="text-secondary">Model: ${cheapest.model_name}</p>
            </div>
        `;
        
        document.getElementById('quick-results').innerHTML = html;
    }
    
    function displayComparisonTable(data) {
        let html = `
            <div class="table-responsive">
                <table class="table table-hover comparison-table">
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th>Model</th>
                            <th>Input Cost</th>
                            <th>Output Cost</th>
                            <th>Toplam Kredi</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        for (const [providerId, models] of Object.entries(data.comparison)) {
            models.forEach(model => {
                const isRecommended = model.total_credits === data.cheapest_option?.total_credits;
                html += `
                    <tr ${isRecommended ? 'class="table-success"' : ''}>
                        <td><strong>Provider ${providerId}</strong></td>
                        <td>${model.model_name}</td>
                        <td><span class="cost-badge badge bg-blue-lt">${model.input_credits}</span></td>
                        <td><span class="cost-badge badge bg-purple-lt">${model.output_credits}</span></td>
                        <td><strong class="cost-badge">${model.total_credits}</strong></td>
                        <td>
                            ${isRecommended ? 
                                '<span class="badge bg-green"><i class="fas fa-star me-1"></i>Önerilen</span>' : 
                                '<span class="badge bg-secondary">Normal</span>'
                            }
                        </td>
                    </tr>
                `;
            });
        }
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        document.getElementById('comparison-table').innerHTML = html;
    }
    
    function displayRecommendations(data) {
        const cheapest = data.cheapest_option;
        let html = '<div class="space-y-2">';
        
        if (cheapest) {
            html += `
                <div class="alert alert-success alert-dismissible">
                    <h4 class="alert-title">💡 Optimizasyon Önerisi</h4>
                    <div class="text-secondary">
                        En ekonomik seçenek: <strong>${cheapest.model_name}</strong> 
                        modelini kullanarak <strong>${cheapest.total_credits} kredi</strong> 
                        ile işleminizi gerçekleştirebilirsiniz.
                    </div>
                </div>
            `;
        }
        
        html += `
            <div class="card">
                <div class="card-body p-3">
                    <h5><i class="fas fa-info-circle me-2"></i>Hesaplama Detayları</h5>
                    <ul class="mb-0">
                        <li>Input: ${data.input_tokens} token</li>
                        <li>Output: ${data.output_tokens} token</li>
                        <li>Toplam: ${data.input_tokens + data.output_tokens} token</li>
                    </ul>
                </div>
            </div>
        `;
        
        html += '</div>';
        document.getElementById('recommendations').innerHTML = html;
    }
    
    function updateCharts(data) {
        updateCostDistributionChart(data);
        updateEfficiencyChart(data);
    }
    
    function updateCostDistributionChart(data) {
        const ctx = document.getElementById('cost-distribution-chart').getContext('2d');
        
        if (costChart) {
            costChart.destroy();
        }
        
        const labels = [];
        const costs = [];
        const colors = [];
        
        for (const [providerId, models] of Object.entries(data.comparison)) {
            models.forEach(model => {
                labels.push(`${model.model_name}`);
                costs.push(model.total_credits);
                colors.push(`hsl(${Math.random() * 360}, 70%, 60%)`);
            });
        }
        
        costChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: costs,
                    backgroundColor: colors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    function updateEfficiencyChart(data) {
        const ctx = document.getElementById('efficiency-chart').getContext('2d');
        
        if (efficiencyChart) {
            efficiencyChart.destroy();
        }
        
        const labels = [];
        const efficiency = [];
        
        for (const [providerId, models] of Object.entries(data.comparison)) {
            models.forEach(model => {
                labels.push(model.model_name);
                // Token başına kredi hesapla
                const totalTokens = data.input_tokens + data.output_tokens;
                efficiency.push((model.total_credits / totalTokens).toFixed(4));
            });
        }
        
        efficiencyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kredi/Token',
                    data: efficiency,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Kredi/Token'
                        }
                    }
                }
            }
        });
    }
    
    function showLoading() {
        document.getElementById('quick-results').innerHTML = `
            <div class="text-center py-4">
                <div class="loading-spinner mx-auto mb-3"></div>
                <p class="text-secondary">Hesaplanıyor...</p>
            </div>
        `;
        
        document.getElementById('comparison-table').innerHTML = `
            <div class="text-center py-4">
                <div class="loading-spinner mx-auto mb-3"></div>
                <p class="text-secondary">Modeller karşılaştırılıyor...</p>
            </div>
        `;
    }
    
    function showError(message) {
        document.getElementById('quick-results').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
    }
    
    function resetForm() {
        document.getElementById('input-tokens').value = 1000;
        document.getElementById('output-tokens').value = 1000;
        document.querySelectorAll('.provider-checkbox').forEach(cb => cb.checked = true);
        
        document.getElementById('quick-results').innerHTML = `
            <div class="text-center text-secondary py-4">
                <i class="fas fa-calculator fa-3x mb-3"></i>
                <p>Hesaplama için parametreleri girin ve "Hesapla" butonuna basın</p>
            </div>
        `;
        
        document.getElementById('comparison-table').innerHTML = `
            <div class="text-center text-secondary py-5">
                <i class="fas fa-table fa-3x mb-3"></i>
                <p>Hesaplama yapıldıktan sonra karşılaştırma tablosu burada görünecek</p>
            </div>
        `;
        
        document.getElementById('recommendations').innerHTML = `
            <div class="text-secondary">
                <small>Hesaplama sonrası öneriler burada görünecek</small>
            </div>
        `;
        
        if (costChart) costChart.destroy();
        if (efficiencyChart) efficiencyChart.destroy();
    }
    
    function exportResults() {
        // Export özelliği henüz implementa edilmedi
        alert('Export özelliği yakında eklenecek');
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
@endpush
@endsection