<div>
    <h1>Tenant Yönetimi</h1>

    <!-- Tenant Tablosu -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Email</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tenants as $tenant)
                <tr>
                    <td>{{ $tenant->id }}</td>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->email }}</td>
                    <td>{{ $tenant->is_active ? 'Aktif' : 'Pasif' }}</td>
                    <td>
                        <button wire:click="editTenant({{ $tenant->id }})" class="btn btn-warning">Düzenle</button>
                        <button wire:click="deleteTenant({{ $tenant->id }})" class="btn btn-danger">Sil</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Düzenleme Formu -->
    @if ($selectedTenant)
        <div class="mt-4">
            <h3>Tenant Düzenle</h3>
            <form wire:submit.prevent="updateTenant">
                <div class="mb-3">
                    <label for="name">Ad</label>
                    <input type="text" id="name" wire:model="name" class="form-control">
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" id="email" wire:model="email" class="form-control">
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label for="phone">Telefon</label>
                    <input type="text" id="phone" wire:model="phone" class="form-control">
                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label for="is_active">Aktif mi?</label>
                    <input type="checkbox" id="is_active" wire:model="is_active">
                    @error('is_active') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Kaydet</button>
                <button type="button" wire:click="resetForm" class="btn btn-secondary">İptal</button>
            </form>
        </div>
    @endif
</div>
