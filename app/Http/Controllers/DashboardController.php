<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoiceView;

class DashboardController extends Controller
{
    public function index()
    {
        $email = Session::get('email');
        $company = Session::get('companyCd');

        $status = Http::get(env('API_GATEWAY') . 'count_status?email=' . $email);
        $data_status = $status->json('Data');
        $trans_pending = $data_status[0]['pending'];
        $trans_done = $data_status[0]['done'];

        $kuota = Http::get(env('API_GATEWAY') . 'getKuota?company_cd=' . $company);
        $data_kuota = $kuota->json('Data');
        $saldo = $data_kuota['saldo'];

        $pending = InvoiceView::all()->count();
        $process = InvoiceDetail::where('process_id', '!=', '0')->count();
        $deliver = InvoiceHeader::where('send_status', '=', 'S')->count();
        $reject = InvoiceHeader::where('send_status', '=', 'F')->count();

        return view('dashboard.index', compact('trans_pending', 'trans_done', 'saldo', 'pending', 'process', 'deliver', 'reject'));
    }

    public function getTable(Request $request)
    {
        $email = Session::get('email');
        $status = $request->status;

        $response = Http::get(env('API_GATEWAY') . 'getTable?email=' . $email . '&status=' . $status);
        $data = $response->json('Data');

        return DataTables::of($data)->make(true);
    }

    public function show(Request $request)
    {
        $data = $request->all();

        $start_date = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Ymd');
        $end_date = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Ymd');

        $process = InvoiceDetail::where('process_id', '!=', '0')
            ->whereRaw('year(gen_date)*10000+month(gen_date)*100+day(gen_date) >= ?', [$start_date])
            ->whereRaw('year(gen_date)*10000+month(gen_date)*100+day(gen_date) <= ?', [$end_date])
            ->count();

        $deliver = InvoiceHeader::where('send_status', '=', 'S')
            ->whereRaw('year(send_date)*10000+month(send_date)*100+day(send_date) >= ?', [$start_date])
            ->whereRaw('year(send_date)*10000+month(send_date)*100+day(send_date) <= ?', [$end_date])
            ->count();

        $reject = InvoiceHeader::where('send_status', '=', 'F')
            ->whereRaw('year(send_date)*10000+month(send_date)*100+day(send_date) >= ?', [$start_date])
            ->whereRaw('year(send_date)*10000+month(send_date)*100+day(send_date) <= ?', [$end_date])
            ->count();

        $dataset[] = array(
            'label' => 'Total Data',
            'data' => [$process, $deliver, $reject],
            'backgroundColor' => '#1b84ff',
            'borderWidth' => 1
        );

        $response = array(
            'labels' => ['Processed', 'Delivered', 'Rejected'],
            'datasets' => $dataset
        );

        return response()->json($response);
    }

    public function showTopUp()
    {
        return view('dashboard.topup');
    }

    public function showHowTopUp()
    {
        return view('dashboard.how_topup');
    }

    // nyalakan jika ingin pembelian e-materai dengan paperID
    public function store(Request $request)
    {
        $data = $request->all();
        $quota = $data['quota'];
        $price = $data['price'];
        $calculated = $data['calculated'];

        $rowID = Session::get('rowID');
        $name = Session::get('name');
        $email = Session::get('email');
        $hp = Session::get('hp');
        $company = Session::get('companyCd');

        $customer = array(
            'id' => strval($rowID),
            'name' => $name,
            'email' => $email,
            'phone' => $hp,
        );

        $item[] = array(
            'name' => "e-Meterai",
            'description' => "Pembelian e-Meterai",
            'quantity' => (int) $quota,
            'price' => (int) $price,
            'discount' => '',
            'tax' => '',
            'additional_info' => '',
        );

        $send = array(
            'email' => false,
            'whatsapp' => false,
            'sms' => false
        );

        if (env('API_GATEWAY') == 'http://emeteraidemo.ifca.co.id/apipaper/api/') {
            $tes = array(
                'invoice_date' => Carbon::now('Asia/Jakarta')->format('d-m-Y'),
                'due_date' => Carbon::now('Asia/Jakarta')->copy()->addMonths(1)->format('d-m-Y'),
                'number' => "INV-" . $company . "-" . Carbon::now('Asia/Jakarta')->format('m') . "-" . Carbon::now('Asia/Jakarta')->format('Y') . "-" . Str::random(8),
                'customer' => $customer,
                'items' => $item,
                'total' => (int) $calculated,
                'send' => $send,
                'company_cd' => $company,
                'pay_cd' => 'TOPUP'
            );

            $response = Http::post(
                env('API_GATEWAY') . 'create_invoice',
                [
                    'invoice_date' => Carbon::now('Asia/Jakarta')->format('d-m-Y'),
                    'due_date' => Carbon::now('Asia/Jakarta')->copy()->addMonths(1)->format('d-m-Y'),
                    'number' => "INV-" . $company . "-" . Carbon::now('Asia/Jakarta')->format('m') . "-" . Carbon::now('Asia/Jakarta')->format('Y') . "-" . Str::random(8),
                    'customer' => $customer,
                    'items' => $item,
                    'total' => (int) $calculated,
                    'send' => $send,
                    'company_cd' => $company,
                    'pay_cd' => 'TOPUP'
                ]
            );
        } else {
            $tes = array(
                'invoice_date' => Carbon::now('Asia/Jakarta')->format('d-m-Y'),
                'due_date' => Carbon::now('Asia/Jakarta')->copy()->addMonths(1)->format('d-m-Y'),
                'number' => "INV-" . $company . "-" . Carbon::now('Asia/Jakarta')->format('m') . "-" . Carbon::now('Asia/Jakarta')->format('Y') . "-" . Str::random(8),
                'customer' => $customer,
                'items' => $item,
                'total' => (int) $calculated,
                'send' => $send,
                'company_cd' => $company,
                'pay_cd' => 'CKFJWG'
            );

            $response = Http::post(
                env('API_GATEWAY') . 'create_invoice_demo',
                [
                    'invoice_date' => Carbon::now('Asia/Jakarta')->format('d-m-Y'),
                    'due_date' => Carbon::now('Asia/Jakarta')->copy()->addMonths(1)->format('d-m-Y'),
                    'number' => "INV-" . $company . "-" . Carbon::now('Asia/Jakarta')->format('m') . "-" . Carbon::now('Asia/Jakarta')->format('Y') . "-" . Str::random(8),
                    'customer' => $customer,
                    'items' => $item,
                    'total' => (int) $calculated,
                    'send' => $send,
                    'company_cd' => $company,
                    'pay_cd' => 'CKFJWG'
                ]
            );
        }

        $callback = $response->json();
        return response()->json($callback);
    }
}
