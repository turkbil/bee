@include('user::helper')
<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Aramak için yazmaya başlayın...">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading wire:target="render, search, perUser, sortBy" class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Sıralama ve Sayfa Adeti Seçimi) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Sıralama Butonları -->
                    <div class="btn-group">
                        <button class="btn btn-icon" wire:click="sortBy('name')" title="Ada göre sırala">
                            <i class="fas fa-sort-alpha-down {{ $sortField === 'name' && $sortDirection === 'asc' ? 'text-primary' : '' }}"></i>
                        </button>
                        <button class="btn btn-icon" wire:click="sortBy('email')" title="E-posta adresine göre sırala">
                            <i class="fas fa-at {{ $sortField === 'email' && $sortDirection === 'asc' ? 'text-primary' : '' }}"></i>
                        </button>
                    </div>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
                        <select wire:model.live="perUser" class="form-select">
                            <option value="8">8</option>
                            <option value="40">40</option>
                            <option value="120">120</option>
                            <option value="480">480</option>
                            <option value="1000">1000</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kullanıcı Kartları -->
        <div class="row row-cards">
            @forelse($users as $user)
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <!-- Avatar (İsim ve Soyisim İlk Harfleri) -->
                        <span class="avatar avatar-xl mb-3 rounded bg-primary text-white">
                            {{ mb_substr($user->name, 0, 1) }}{{ mb_substr($user->surname, 0, 1) }}
                        </span>
                        <!-- Kullanıcı Bilgileri -->
                        <h3 class="m-0 mb-1"><a href="#">{{ $user->name }} {{ $user->surname }}</a></h3>
                        <div class="text-secondary">{{ $user->email }}</div>
                        <!-- Durum ve Kullanıcı ID -->
                        <div class="mt-3">
                            <span class="badge {{ $user->is_active ? 'bg-green-lt' : 'bg-red-lt' }}">
                                {{ $user->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                            <span class="badge bg-blue-lt ms-2">ID: {{ $user->id }}</span>
                        </div>
                    </div>
                    <!-- İşlem Butonları -->
                    <div class="d-flex">
                        <a href="{{ route('admin.user.manage', $user->id) }}" class="card-btn">
                            <i class="fas fa-edit me-2 text-muted"></i>
                            Düzenle
                        </a>
                        <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', { userId: {{ $user->id }}, userName: '{{ $user->name }} {{ $user->surname }}' })" class="card-btn text-danger">
                            <i class="fas fa-trash me-2"></i>
                            Sil
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <p class="empty-title">Kayıt bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Arama kriterlerinize uygun kayıt bulunmamaktadır.
                    </p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
    <!-- Pagination -->
    {{ $users->links() }}
</div>
