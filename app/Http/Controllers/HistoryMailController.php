<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

use App\Mail\InvoiceMail;

use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoiceLog;
use App\Models\ArLedger;
use App\Models\Admin\EmailConfiguration;

class HistoryMailController extends Controller
{
    public function index()
    {
        return view('invoice_history.index');
    }

    public function getTable($status)
    {
        $data = InvoiceHeader::where('process_id', '!=', '0')
            ->where('send_status', '=', $status);
        return DataTables::of($data)->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data_email = EmailConfiguration::first();

        if (!is_null($data_email)) {
            $decryptPass = Crypt::decryptString($data_email->password);

            $config = array(
                'driver' => $data_email->driver,
                'host' => $data_email->host,
                'port' => $data_email->port,
                'username' => $data_email->username,
                'password' => $decryptPass,
                'encryption' => $data_email->encryption,
                'from' => array('address' => $data_email->sender_email, 'name' => $data_email->sender_name),
            );
            Config::set('mail', $config);

            $dt = $data['models'];
            if (!empty($dt)) {
                for ($i = 0; $i < count($dt); $i++) {
                    $email = trim(strtolower($dt[$i]['email_addr']));

                    $process_id = $dt[$i]['process_id'];
                    $invoice_detail = InvoiceDetail::where('process_id', '=', $process_id)
                        ->where('email_addr', 'LIKE', "%{$email}%")
                        ->first();
                    $filenames = $invoice_detail->filenames;

                    $attr = $this->attributeEmail($invoice_detail);

                    $where = array(
                        'process_id' => $process_id,
                        'email_addr' => $email
                    );

                    $data_log_msg_success = array(
                        'status_code' => '200',
                        'response_message' => 'Email sent successfully to: ' . $email,
                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                    );

                    $data_log_msg_not_found = array(
                        'status_code' => '404',
                        'response_message' => 'No email address was given.',
                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                    );

                    $data_hdr_success = array(
                        'status_process' => 'S',
                        'send_status' => 'S',
                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                    );

                    $data_hdr_failed = array(
                        'status_process' => 'S',
                        'send_status' => 'F',
                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                    );

                    $criteria_pb = array(
                        'entity_cd' => $dt[$i]['entity_cd'],
                        'project_no' => $dt[$i]['project_no'],
                        'debtor_acct' => $dt[$i]['debtor_acct'],
                        'doc_no' => $dt[$i]['doc_no'],
                    );

                    $data_pb_success = array(
                        'status_email' => 'Y',
                        'status_proses' => 'Success',
                        'status_gen' => 'Y',
                    );

                    $data_pb_failed = array(
                        'status_email' => 'F',
                        'status_proses' => 'Success',
                        'status_gen' => 'S',
                    );

                    $filePath = env('ROOT_INVOICE_FILE_PATH') . 'invoice/HISTORY_INVOICE/' . $filenames;
                    $headers = get_headers($filePath);

                    if ($headers && strpos($headers[0], '200 OK') !== false) {
                        if (str_contains($email, ';')) {
                            // Jika ada tanda titik koma
                            $pecah_email = explode(";", $email);

                            try {
                                if (!empty($pecah_email)) {
                                    Mail::to($pecah_email)->send(new InvoiceMail($attr));

                                    InvoiceHeader::where($where)->update($data_hdr_success);

                                    InvoiceLog::where($where)->update($data_log_msg_success);

                                    ArLedger::where($criteria_pb)->update($data_pb_success);
                                } else {
                                    InvoiceHeader::where($where)->update($data_hdr_failed);

                                    InvoiceLog::where($where)->update($data_log_msg_not_found);

                                    ArLedger::where($criteria_pb)->update($data_pb_failed);
                                }
                            } catch (\Exception $e) {
                                $data_log_msg = array(
                                    'status_code' => '400',
                                    'response_message' => 'Failed to send email: ' . $e->getMessage(),
                                    'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                );

                                InvoiceLog::where($where)->update($data_log_msg);

                                ArLedger::where($criteria_pb)->update($data_pb_failed);
                            }
                        } else {
                            // Jika tidak ada tanda titik koma
                            try {
                                if (isset($email) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($email)->send(new InvoiceMail($attr));

                                    InvoiceHeader::where($where)->update($data_hdr_success);

                                    InvoiceLog::where($where)->update($data_log_msg_success);

                                    ArLedger::where($criteria_pb)->update($data_pb_success);
                                } else {
                                    InvoiceHeader::where($where)->update($data_hdr_failed);

                                    InvoiceLog::where($where)->update($data_log_msg_not_found);

                                    ArLedger::where($criteria_pb)->update($data_pb_failed);
                                }
                            } catch (\Exception $e) {
                                $data_log_msg = array(
                                    'status_code' => '400',
                                    'response_message' => 'Failed to send email: ' . $e->getMessage(),
                                    'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                );

                                InvoiceLog::where($where)->update($data_log_msg);

                                ArLedger::where($criteria_pb)->update($data_pb_failed);
                            }
                        }

                        $response = array(
                            "Error" => false,
                            "Pesan" => "Email send successfully"
                        );
                    } else {
                        // File does not exist."
                        $response = array(
                            "Error" => true,
                            "Pesan" => "Unable to process email, because the file does not exist"
                        );
                    }
                }
            }
        } else {
            $response = array(
                "Error" => true,
                "Pesan" => "Email configuration not created."
            );
        }

        return response()->json($response);
    }

    protected function attributeEmail($param)
    {
        $statement = "EXEC mgr.xrl_send_mail_output ?, ?, ?, ?, ?";

        $exec = DB::connection('sqlsrv')->select(
            $statement,
            [trim($param['entity_cd']), trim($param['project_no']), $param['debtor_acct'], $param['doc_no'], '']
        );

        $data = array(
            'entity_cd' => $exec[0]->entity_cd,
            'project_no' => $exec[0]->project_no,
            'subject' => $exec[0]->subject,
            'name' => $exec[0]->name,
            'lot_descs' => $exec[0]->lot_descs,
            'descs' => $exec[0]->descs,
            'amount' => $exec[0]->amount,
            'jatuh_tempo' => $exec[0]->jatuh_tempo,
            'tahun' => $exec[0]->tahun,
            'va_mandiri' => $exec[0]->va_mandiri,
            'va_bca' => $exec[0]->va_bca,
            'va_permata' => $exec[0]->va_permata,
            'va_tokped' => $exec[0]->va_tokped,
            'filenames' => $param['filenames'],
            'process_id' => $param['process_id']
        );

        return $data;
    }

    public function show($process_id, $email_addr)
    {
        $data = InvoiceDetail::where('process_id', '=', $process_id)
            ->where('email_addr', 'LIKE', "%{$email_addr}%")
            ->get();
        return response()->json($data);
    }
}
