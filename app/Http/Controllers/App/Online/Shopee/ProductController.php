<?php

namespace App\Http\Controllers\app\Online\Shopee;

use App\Helpers\ApiService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ApiServiceShopee;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function daftarPartNumber(Request $request)
    {

        if(strtoupper(trim($request->session()->get('app_user_role_id'))) == 'MD_REQ_API') {
            return redirect()->back()->withInput()->with('failed', 'Anda tidak memiliki akses untuk membuka halaman ini');
        }
        $view = view('layouts.online.shopee.product.product', [
            'title_menu'    => 'Products'
        ]);

        if (!empty($request->get('part_number')) && $request->get('part_number') != '' && $request->ajax()) {
            
            $responseApi = ApiServiceShopee::SearchProductByPartNumber(
                strtoupper(trim($request->get('part_number'))),
                strtoupper(trim($request->session()->get('app_user_company_id')))
            );
            
            $statusApi = json_decode($responseApi)->status;
            if ($statusApi == 1) {
                return response()->json([
                    'status'    => 1,
                    'message'   => 'success',
                    'data'      => Str::between($view->with('data_all', json_decode($responseApi)->data)->render(), '<!--start::container-->', '<!--end::container-->')
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => json_decode($responseApi)->message,
                    'data'      =>  ''
                ]);
            }
        } else {
            return $view->with('data_all', (object)[
                'status'    => 0,
                'message'   => 'Belum ada data yang dicari',
                'data'      => []
            ]);
        }
    }

    public function cekProductId(Request $request)
    {
        $responseApi = ApiServiceShopee::CekProductId(
            strtoupper(trim($request->get('product_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        $statusApi = json_decode($responseApi)->status;

        if ($statusApi == 1) {
            $dataApi = json_decode($responseApi)->data;

            $view = view('layouts.online.shopee.product.edit_info', [
                'title_menu'    => 'Products',
                'dataApi'       => $dataApi
            ]);

            return [
                'status'    => 1,
                'message'   => 'success',
                'data'      => $view->render()
            ];
        } else {
            return json_decode($responseApi, true);
        }
    }

    public function updateProductId(Request $request)
    {
        $responseApi = ApiServiceShopee::UpdateShopeeidInPart(
            strtoupper(trim($request->get('part_number'))),
            strtoupper(trim($request->get('product_id'))),
            strtoupper(trim($request->session()->get('app_user_company_id')))
        );
        return json_decode($responseApi, true);
    }
}
