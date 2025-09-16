@extends('admin.layout')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                {{ __('tenantmanagement::admin.failed_jobs') }}
            </h1>
        </div>
        <div class="col-auto">
            <div class="btn-list">
                <a href="#" class="btn btn-outline-danger" onclick="clearAllFailedJobs()">
                    <i class="fas fa-trash"></i> Tümünü Temizle
                </a>
                <a href="#" class="btn btn-outline-primary" onclick="retryAllFailedJobs()">
                    <i class="fas fa-redo"></i> Tümünü Yeniden Dene
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-vcenter" id="failed-jobs-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Connection</th>
                        <th>Queue</th>
                        <th>Payload (İlk 100 karakter)</th>
                        <th>Exception (İlk 150 karakter)</th>
                        <th>Failed At</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody id="failed-jobs-body">
                    <!-- Failed jobs buraya yüklenecek -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Sayfa yüklendiğinde failed jobs'ları getir
document.addEventListener('DOMContentLoaded', function() {
    loadFailedJobs();
});

async function loadFailedJobs() {
    try {
        const response = await fetch('/admin/api/failed-jobs');
        const jobs = await response.json();
        
        const tbody = document.getElementById('failed-jobs-body');
        
        if (jobs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Başarısız iş bulunamadı 🎉</td></tr>';
            return;
        }
        
        tbody.innerHTML = jobs.map(job => `
            <tr>
                <td><code>${job.id}</code></td>
                <td><span class="badge bg-info">${job.connection}</span></td>
                <td><span class="badge bg-warning">${job.queue}</span></td>
                <td><small>${job.payload.substring(0, 100)}...</small></td>
                <td><small class="text-danger">${job.exception.substring(0, 150)}...</small></td>
                <td><small>${new Date(job.failed_at).toLocaleString('tr-TR')}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="retryJob('${job.id}')">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteJob('${job.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
        
    } catch (error) {
        console.error('Failed jobs yüklenemedi:', error);
        document.getElementById('failed-jobs-body').innerHTML = 
            '<tr><td colspan="7" class="text-center text-danger py-4">Veriler yüklenemedi. Konsolu kontrol edin.</td></tr>';
    }
}

async function retryJob(jobId) {
    try {
        await fetch(`/admin/api/failed-jobs/${jobId}/retry`, { method: 'POST' });
        loadFailedJobs(); // Tabloyu yenile
        showNotification('İş yeniden kuyruğa eklendi', 'success');
    } catch (error) {
        showNotification('İş yeniden denenirken hata oluştu', 'danger');
    }
}

async function deleteJob(jobId) {
    if (!confirm('Bu başarısız işi silmek istediğinize emin misiniz?')) return;
    
    try {
        await fetch(`/admin/api/failed-jobs/${jobId}`, { method: 'DELETE' });
        loadFailedJobs(); // Tabloyu yenile
        showNotification('İş silindi', 'success');
    } catch (error) {
        showNotification('İş silinirken hata oluştu', 'danger');
    }
}

async function clearAllFailedJobs() {
    if (!confirm('TÜM başarısız işleri silmek istediğinize emin misiniz?')) return;
    
    try {
        await fetch('/admin/api/failed-jobs', { method: 'DELETE' });
        loadFailedJobs(); // Tabloyu yenile
        showNotification('Tüm başarısız işler silindi', 'success');
    } catch (error) {
        showNotification('İşler silinirken hata oluştu', 'danger');
    }
}

async function retryAllFailedJobs() {
    if (!confirm('TÜM başarısız işleri yeniden denemek istediğinize emin misiniz?')) return;
    
    try {
        await fetch('/admin/api/failed-jobs/retry', { method: 'POST' });
        loadFailedJobs(); // Tabloyu yenile
        showNotification('Tüm başarısız işler yeniden kuyruğa eklendi', 'success');
    } catch (error) {
        showNotification('İşler yeniden denenirken hata oluştu', 'danger');
    }
}

function showNotification(message, type) {
    // Basit notification sistemi
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // 5 saniye sonra otomatik kaldır
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}
</script>
@endsection