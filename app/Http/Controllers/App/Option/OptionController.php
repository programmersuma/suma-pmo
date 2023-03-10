<?php

namespace App\Http\Controllers\App\Option;

use App\Helpers\ApiService;
use Illuminate\Support\Str;

use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class OptionController extends Controller
{
    public function optionDealer(Request $request)
    {
        $responseApi = ApiService::optionDealer($request->get('search'), $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataDealerSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_dealer.'</td>
                        <td>'.$data->nama_dealer.'</td>
                        <td class="text-center">
                            <button type="button" id="selectedOptionDealer" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_dealer="'.$data->kode_dealer.'" data-nama_dealer="'.$data->nama_dealer.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<table id="tableSearchDealer" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Dealer</th>
                                <th class="min-w-150px">Nama Dealer</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageDealer" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionDealer" name="selectPerPageOptionDealer" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionDealerInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionDealer">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionDealerSalesman(Request $request)
    {
        $responseApi = ApiService::optionDealerSalesman($request->get('salesman'), $request->get('search'),
                            $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataDealerSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';



            foreach ($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_dealer.'</td>
                        <td>'.$data->nama_dealer.'</td>
                        <td class="text-center">
                            <button id="selectedOptionDealerSalesman" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_dealer="'.$data->kode_dealer.'" data-nama_dealer="'.$data->nama_dealer.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }


            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<table id="tableSearchDealerSalesman" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Dealer</th>
                                <th class="min-w-150px">Nama Dealer</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageDealerSalesman" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionDealerSalesman" name="selectPerPageOptionDealerSalesman" aria-controls="selectPerPageOptionSalesman"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionDealerSalesmanInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionDealerSalesman">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionPartNumber(Request $request)
    {

        $Agent = new Agent();
        $responseApi = ApiService::optionPartNumber($request->get('search'),
                            $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));

        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataPartNumber = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page,
            );

            $table_row = '';
            $table_pagination = '';

            if ($Agent->isDesktop()) {
                foreach ($dataPartNumber as $data) {
                    $table_row .= '<tr>
                            <td>'.strtoupper(trim($data->part_number)).'</td>
                            <td>'.trim($data->description).'</td>
                            <td>'.strtoupper(trim($data->produk)).'</td>
                            <td class="text-end">'.number_format($data->het).'</td>
                            <td class="text-center">
                                <button id="selectedOptionPartNumber" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                    data-part_number="'.strtoupper(trim($data->part_number)).'" data-nama_part="'.trim($data->description).'"
                                    data-produk="'.strtoupper(trim($data->produk)).'" data-het="'.number_format($data->het).'">
                                    <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                                </button>
                            </td>
                        </tr>';
                }
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                        <span class="page-link">'.trim($label).'</span></span>
                    </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                        <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                    </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="5" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            // cek apakah isdektrop
        if ($Agent->isDesktop()) {
            $table_header = '<table id="tableSearchPartNumber" class="table align-middle table-row-bordered fs-6">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-200px">Part Number</th>
                            <th class="min-w-100px">Nama Part</th>
                            <th class="w-50px">Produk</th>
                            <th class="w-50px text-end">HET</th>
                            <th class="min-w-50px text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                </table>';
        } else {
            $table_header = '';
            foreach ($dataPartNumber as $data) {
            $table_header .= '<div id="selectedOptionPartNumber" class="card mb-3 border-2 shadow-sm" style="max-width: 540px;" data-part_number="'.strtoupper(trim($data->part_number)).'" data-nama_part="'.trim($data->description).'"data-produk="'.strtoupper(trim($data->produk)).'" data-het="'.number_format($data->het).'">
                                <div class="row g-0">
                                    <div class="col-md-4 col-4">
                                    <img src="http://43.252.9.117/suma-images/'.strtoupper(trim($data->part_number)).'.jpg" class="img-fluid rounded-start max-width-540 overflow-hidden" alt="'.strtoupper(trim($data->part_number)).'" onerror="defaultImage(this)">
                                    </div>
                                    <div class="col-md-8 col-8">
                                        <div class="card-body">
                                        <span class="card-title fs-5 text-dark fw-bolder">'.strtoupper(trim($data->part_number)).'</span>
                                        <span class="card-title fs-6 text-muted fw-bold d-block">'.trim($data->description).'</span>
                                        <span class="card-text fs-4 text-dark fw-bolder mt-4 d-block">HET : Rp '.number_format($data->het).'</span>
                                        <span class="card-text fs-6 text-muted fw-bold d-block">Produk : '.strtoupper(trim($data->produk)).'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            }
        }

            $table_header .= '<div id="pagePartNumber" class="mt-5">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length">
                                <label>
                                    <select id="selectPerPageOptionPartNumber" name="selectPerPageOptionPartNumber" aria-controls="selectPerPageOption"
                                        class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                        <option value="10"'.$table_per_page10.'>10</option>
                                        <option value="25"'.$table_per_page25.'>25</option>
                                        <option value="50"'.$table_per_page50.'>50</option>
                                        <option value="100"'.$table_per_page100.'>100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="dataTables_info" id="selectPerPageOptionPartNumberInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionPartNumber">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionPartNumber">
                                <ul class="pagination">'.$table_pagination.'</ul>
                            </div>
                        </div>
                    </div>
                </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return redirect()->back()->withInput()->with('failed', $messageApi);
        }
    }

    public function optionSalesman(Request $request)
    {
        $responseApi = ApiService::optionSalesman($request->get('search'), $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataSales as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_sales.'</td>
                        <td>'.$data->nama_sales.'</td>
                        <td class="text-center">
                            <button id="selectedOptionSalesman" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_sales="'.$data->kode_sales.'" data-nama_sales="'.$data->nama_sales.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                        <span class="page-link">'.trim($label).'</span></span>
                    </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                        <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                    </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<table id="tableSearchSalesman" class="table align-middle table-row-bordered fs-6">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Kode Sales</th>
                            <th class="min-w-150px">Nama Sales</th>
                            <th class="min-w-50px text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                </table>
                <div id="pageSalesman" class="mt-5">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length">
                                <label>
                                    <select id="selectPerPageOptionSalesman" name="selectPerPageOptionSalesman" aria-controls="selectPerPageOption"
                                        class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                        <option value="10"'.$table_per_page10.'>10</option>
                                        <option value="25"'.$table_per_page25.'>25</option>
                                        <option value="50"'.$table_per_page50.'>50</option>
                                        <option value="100"'.$table_per_page100.'>100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="dataTables_info" id="selectPerPageOptionSalesmanInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionSalesman">
                                <ul class="pagination">'.$table_pagination.'</ul>
                            </div>
                        </div>
                    </div>
                </div>';


            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionSupervisor(Request $request)
    {
        $responseApi = ApiService::optionSupervisor($request->get('search'), $request->get('page'),
                            $request->get('per_page'), strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataSupervisor = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataSupervisor as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_spv.'</td>
                        <td>'.$data->nama_spv.'</td>
                        <td class="text-center">
                            <button id="selectedOptionSupervisor" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_spv="'.$data->kode_spv.'" data-nama_spv="'.$data->nama_spv.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                        <span class="page-link">'.trim($label).'</span></span>
                    </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                        <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                    </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<table id="tableSearchSupervisor" class="table align-middle table-row-bordered fs-6">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Kode SPV</th>
                            <th class="min-w-150px">Nama SPV</th>
                            <th class="min-w-50px text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                </table>
                <div id="pageSupervisor" class="mt-5">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length">
                                <label>
                                    <select id="selectPerPageOptionSupervisor" name="selectPerPageOptionSupervisor" aria-controls="selectPerPageOption"
                                        class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                        <option value="10"'.$table_per_page10.'>10</option>
                                        <option value="25"'.$table_per_page25.'>25</option>
                                        <option value="50"'.$table_per_page50.'>50</option>
                                        <option value="100"'.$table_per_page100.'>100</option>
                                    </select>
                                </label>
                            </div>
                            <div class="dataTables_info" id="selectPerPageOptionSupervisorInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionSupervisor">
                                <ul class="pagination">'.$table_pagination.'</ul>
                            </div>
                        </div>
                    </div>
                </div>';


            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionTipeMotor(Request $request)
    {
        $responseApi = ApiService::OptionTipeMotor($request->get('search'), $request->get('page'), $request->get('per_page'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;
            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataDealerSales = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataDealerSales as $data) {
                $table_row .= '<tr>
                        <td>'.strtoupper(trim($data->kode)).'</td>
                        <td>'.strtoupper(trim($data->keterangan)).'</td>
                        <td class="text-center">
                            <button id="selectedOptionTipeMotor" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode="'.strtoupper(trim($data->kode)).'" data-keterangan="'.strtoupper(trim($data->keterangan)).'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<table id="tableSearchTipeMotor" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode</th>
                                <th class="min-w-150px">Keterangan</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageTipeMotor" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionTipeMotor" name="selectPerPageOptionTipeMotor" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionTipeMotorInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionTipeMotor">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionGroupProduk(Request $request)
    {
        $responseApi = ApiService::OptionGroupProduk($request->get('level'), $request->get('search'),
                            $request->get('page'), $request->get('per_page'));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataGroupProduk = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataGroupProduk as $data) {
                $table_row .= '<tr>
                        <td>'.$data->kode_produk.'</td>
                        <td>'.$data->keterangan.'</td>
                        <td class="text-center">
                            <button id="selectedOptionProduk" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-kode_produk="'.$data->kode_produk.'" data-keterangan="'.$data->keterangan.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<table id="tableSearchProduk" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Kode Produk</th>
                                <th class="min-w-150px">Nama Produk</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageProduk" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionProduk" name="selectPerPageOptionProduk" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionProdukInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionGroupProduk">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }

    public function optionUpdateHarga(Request $request)
    {
        $responseApi = ApiService::OptionUpdateHarga($request->get('lokasi'), $request->get('page'),
                            $request->get('per_page'), $request->get('search'),
                            strtoupper(trim($request->session()->get('app_user_company_id'))));
        $statusApi = json_decode($responseApi)->status;
        $messageApi =  json_decode($responseApi)->message;

        if ($statusApi == 1) {
            $data = json_decode($responseApi)->data;

            $data_per_page = $data->per_page;
            $data_link_page = $data->links;
            $data_from_record = $data->from;
            $data_to_record = $data->to;
            $data_total_record = $data->total;

            $dataUpdateHarga = new LengthAwarePaginator(
                array_values($data->data),
                $data->total,
                $data->per_page,
                $data->current_page
            );

            $table_row = '';
            $table_pagination = '';

            foreach ($dataUpdateHarga as $data) {
                $table_row .= '<tr>
                        <td>'.$data->tanggal.'</td>
                        <td>'.$data->kode.'</td>
                        <td class="text-center">
                            <button id="selectedOptionUpdateHarga" class="btn btn-icon btn-bg-primary btn-sm me-1"
                                data-tanggal="'.$data->tanggal.'" data-kode="'.$data->kode.'">
                                <i class="fa fa-check text-white" data-toggle="tooltip" data-placement="top" title="Select"></i>
                            </button>
                        </td>
                    </tr>';
            }

            foreach ($data_link_page as $data) {
                $page = '';
                if(!empty($data->url)) {
                    $pages = explode("?page=", $data->url);
                    $page = $pages[1];
                }

                $label = $data->label;
                $disabled = ($data->url == null) ? 'disabled' : '';
                $active = ($data->active == true) ? 'active' : '';
                $item = 'page-item';

                if (Str::contains(trim($data->label), 'Previous')) {
                    $label = '<';
                    $item = 'page-item previous';
                }

                if (Str::contains(trim($data->label), 'Next')) {
                    $label = '>';
                    $item = 'page-item next';
                }

                if ($data->url == null) {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($disabled)).'">
                            <span class="page-link">'.trim($label).'</span></span>
                        </li>';
                } else {
                    $table_pagination .= '<li class="'.trim(trim($item).' '.trim($active).' '.trim($disabled)).'">
                            <a href="#" class="page-link" data-page="'.trim($page).'">'.trim($label).'</a>
                        </li>';
                }
            }

            $table_per_page10 = '';
            $table_per_page25 = '';
            $table_per_page50 = '';
            $table_per_page100 = '';
            if ($data_per_page == '10') {
                $table_per_page10 = 'selected';
            } elseif ($data_per_page == '25') {
                $table_per_page25 = 'selected';
            } elseif ($data_per_page == '50') {
                $table_per_page50 = 'selected';
            } elseif ($data_per_page == '100') {
                $table_per_page100 = 'selected';
            }

            if($table_row == '') {
                $table_row .= '<tr>
                            <td colspan="3" class="pt-12 pb-12">
                            <div class="row text-center pe-10">
                                <span class="svg-icon svg-icon-muted">
                                    <svg class="h-100px w-100px" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="currentColor"/>
                                        <path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="row text-center pt-8">
                                <span class="fs-6 fw-bolder text-gray-500">-  Tidak ada data yang ditampilkan -</span>
                            </div>
                        </td>
                    </tr>';
            }

            $table_header = '<table id="tableSearchUpdateHarga" class="table align-middle table-row-bordered fs-6">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px">Tanggal</th>
                                <th class="min-w-150px">Kode</th>
                                <th class="min-w-50px text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7 fw-bold text-gray-800">'.$table_row.'</tbody>
                    </table>
                    <div id="pageUpdateHarga" class="mt-5">
                        <div class="row">
                            <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="dataTables_length">
                                    <label>
                                        <select id="selectPerPageOptionUpdateHarga" name="selectPerPageOptionUpdateHarga" aria-controls="selectPerPageOption"
                                            class="form-select form-select-sm" data-control="select2" data-hide-search="true">
                                            <option value="10"'.$table_per_page10.'>10</option>
                                            <option value="25"'.$table_per_page25.'>25</option>
                                            <option value="50"'.$table_per_page50.'>50</option>
                                            <option value="100"'.$table_per_page100.'>100</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="dataTables_info" id="selectPerPageOptionUpdateHargaInfo" role="status" aria-live="polite">Showing <span id="startRecordOptionSalesman">'.$data_from_record.'</span> to '.$data_to_record.' of '.$data_total_record.' records</div>
                            </div>
                            <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="paginationOptionUpdateHarga">
                                    <ul class="pagination">'.$table_pagination.'</ul>
                                </div>
                            </div>
                        </div>
                    </div>';

            return response()->json(['status' => 1, 'message' => 'success', 'data' => $table_header]);
        } else {
            return response()->json(['status' => 0, 'message' => $messageApi]);
        }
    }
}
