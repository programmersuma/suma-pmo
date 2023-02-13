@extends('layouts.main.index')
@section('title','Online')
@section('subtitle','Shopee')
@push('styles')
@endpush
@section('container')
<div class="card card-flush">
    <div class="card-header align-items-center border-0 mt-4 mb-4">
        <div class="align-items-start flex-column">
            <!--begin::Input group-->
            <div class="position-relative w-md-400px me-md-2">
                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                <span class="svg-icon svg-icon-3 svg-icon-gray-500 position-absolute top-50 translate-middle ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <input type="text" class="form-control form-control-solid ps-10" name="search" id="filterSearch" value="" oninput="this.value = this.value.toUpperCase()" placeholder="Search">
            </div>
            <!--end::Input group-->
        </div>
        {{-- <div class="d-flex align-items-center ms-3">
            <button type="reset" class="btn btn-primary" id="btn-adddiskonproduk" data-bs-toggle="modal" data-bs-target="#tambah_diskon"><i class="bi bi-plus-circle fs-1"></i> Tambah</button>
        </div> --}}
        <div class="card-toolbar">
            <div class="input-group">
                <input type="text" id="get_start_date" class="form-control" placeholder="Start Date" aria-label="Start Date">
                <span class="input-group-text">SD</span>
                <input type="text" id="get_end_date" class="form-control" placeholder="End Date" aria-label="End Date">
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap flex-stack my-4" data-select2-id="select2-data-131-enac">
    <!--begin::Title-->
    <div class="d-flex flex-wrap align-items-center my-1">
                            {{-- $data_disc_dealer->total --}}
        <h3 class="fw-bolder me-5 my-1">Daftar Pemindahan Stock Antar lokasi Shopee
        <span class="text-gray-400 fs-6">↓</span></h3>
    </div>
    <!--end::Title-->
</div>

<div class="tab-content">
    @if (\Agent::isDesktop())
    <!--begin::Card-->
    <div id="view_table" class="tab-pane fade active show">
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Table container-->
                <div class="table-responsive mt-10">
                    <!--begin::Table-->
                    <div id="daftar_table" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        {{-- table --}}
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <select class="form-select form-select-sm form-select-solid" id="per_page">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="view_daftar_paginat">
                                    {{-- paginatesen --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Table-->
                </div>
            <!--end::Table container-->
            </div>
        <!--end::Card body-->
        </div>
    </div>
    <!--end::Card-->
    @else
    <!--begin::Tab pane-->
    <div id="daftar_table" class="tab-pane fade active show">
        
    </div>
    <div class="card mt-6 p-5">
        <div class="row">
            <div class="col-4 d-flex align-items-center justify-content-center justify-content-md-start">
                <div class="dataTables_length">
                    <select class="form-select form-select-sm form-select-solid" id="per_page">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <!--end::Card-->
            </div>
            <div class="col-8 d-flex align-items-center justify-content-center justify-content-md-end">
                <div class="dataTables_paginate paging_simple_numbers" id="view_daftar_paginat">
                    {{-- paginatesen --}}
                </div>
                <!--end::Owner-->
            </div>
        </div>
    </div>
    <!--end::Tab pane-->
    @endif
</div>



    @push('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script language="JavaScript" src="{{ asset('assets/js/suma/online/shopee/daftarPemindahan.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
