{{-- resources/views/admin/partials/breadcrumb.blade.php --}}
@if(Route::currentRouteName() !== 'admin.dashboard')
<div class="page-header">
    <div class="container-xl">
        <div class="row">
            <div class="col">
                {{-- Breadcrumb içeriği modules/page'den alınır --}}
                @includeIf('page::helper', ['section' => 'breadcrumb'])
            </div>
            <div class="col-auto position-relative">
                {{-- Module menu içeriği modules/page'den alınır --}}
                @includeIf('page::helper', ['section' => 'module-menu'])
            </div>
        </div>
    </div>
</div>
@endif
