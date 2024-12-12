@extends('admin.layout')
@include('page::helper')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-mode">
            <input type="checkbox" id="table-switch" class="table-switch">
            <div class="app">
                <div class="switch-content">
                    <div class="switch-label"></div>
                    <label for="table-switch">
                        <div class="toggle"></div>
                        <div class="names">
                            <p class="large" data-bs-toggle="tooltip" data-bs-placement="right" title="Satırları daralt">
                                <i class="fa-thin fa-table-cells fa-lg fa-fade" style="--fa-animation-duration: 2s;"></i>
                            </p>
                            <p class="small" data-bs-toggle="tooltip" data-bs-placement="right" title="Satırları genişlet">
                                <i class="fa-thin fa-table-cells-large fa-lg fa-fade" style="--fa-animation-duration: 2s;"></i>
                            </p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <table id="table"
               class="table table-hover{{ (isset($_COOKIE['table']) && $_COOKIE['table'] == '1') ? ' table-sm' : '' }}"
               data-toggle="table"
               data-pagination="true"
               data-search="true"
               data-side-pagination="server"
               data-page-list="[10, 50, 100, 500, 1000]"
               data-cookie="true"
               data-cookie-id-table="pages-table"
               data-cookie-expires="30"
               data-pagination-parts='["pageSize", "pageList"]'
               data-url="{{ route('admin.page.list') }}?sort=created_at&order=desc"
               data-sortable="true">
            <thead>
                <tr>
                    <th data-field="page_id" data-sortable="true" data-width="25" data-width-unit="px" class="d-none d-lg-table-cell f12">ID</th>
                    <th data-field="title" data-sortable="true" data-width="78" data-width-unit="%">Başlık</th>
                    <th data-field="is_active" data-sortable="true" data-width="10" data-width-unit="%" data-formatter="activeFormatter" class="text-center">Durum</th>
                    <th data-field="operate" data-width="10" data-width-unit="%" class="text-center" data-formatter="operateFormatter">İşlemler</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('js')
<script>
    function activeFormatter(value, row) {
        return `<button type="button"
                  class="btn btn-link p-0 toggle-status"
                  onclick="toggleStatus(${row.page_id})"
                  title="${value == 1 ? 'Aktif' : 'Pasif'}">
                <i class="fa-regular fa-lg ${value == 1 ? 'fa-circle-check text-success' : 'fa-circle-xmark text-danger'}"></i>
               </button>`;
    }

    function operateFormatter(value, row) {
        return `
            <div class="container">
                <div class="row">
                    <div class="col">
                        <a href="/admin/page/manage/${row.page_id}" data-bs-toggle="tooltip" data-bs-placement="top" title="Düzenle">
                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                        </a>
                    </div>
                    <div class="col lh-1">
                        <div class="dropdown mt-1">
                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item link-danger"> Sil </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Global değişkenler
    var $table = $('#table');

    // Toggle status function
    function toggleStatus(pageId) {
        const $button = $(`.toggle-status[onclick*="${pageId}"]`);
        const $icon = $button.find('i');

        // Loading durumunu göster
        $icon.removeClass('fa-circle-check fa-circle-xmark text-success text-danger')
             .addClass('fa-spinner fa-spin');
        $button.prop('disabled', true);

        // Livewire metodunu çağır
        Livewire.dispatch('toggleActive', pageId);
    }

    // Initialize
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('tableUpdated', () => {
            $table.bootstrapTable('refresh');
        });
    });
</script>
@endpush
