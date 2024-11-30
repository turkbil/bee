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
        <table id="table" class="table table-hover{{ (isset($_COOKIE['table']) && $_COOKIE['table'] == '1') ? ' table-sm' : '' }}" data-toggle="table" data-pagination="true" data-search="true" data-sortable="true" data-page-list="[10, 50, 100, 500, 1000]" data-cookie="true" data-cookie-id-table="pages-table" data-cookie-expires="30">
            <thead>
                <tr>
                    <th data-field="page_id" data-width="25" data-width-unit="px" class="d-none d-lg-table-cell f12">ID</th>
                    <th data-field="title" data-width="88" data-width-unit="%">Başlık</th>
                    <th data-field="date" data-width="10" data-width-unit="%" class="text-center">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                <tr id="row-{{ $page->page_id }}">
                    <td><span class="badge">{{ $page->page_id }}</span></td>
                    <td>{{ $page->title }}</td>
                    <td>
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <a href="{{ route('admin.page.manage', $page->page_id) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Düzenle">
                                        <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                    </a>
                                </div>
                                <div class="col lh-1">
                                    <div class="dropdown mt-1">
                                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item btn-delete link-danger" data-module="page" data-id="{{ $page->page_id }}" data-title="{{ $page->title }}"> Sil </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('js')
@endpush
