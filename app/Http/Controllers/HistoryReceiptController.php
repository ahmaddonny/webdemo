<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;

use App\Mail\ReceiptMail;

use App\Models\ReceiptView;
use App\Models\ReceiptHeader;
use App\Models\ReceiptDetail;
use App\Models\ReceiptLog;
use App\Models\Admin\EmailConfiguration;

class HistoryReceiptController extends Controller
{
    public function index()
    {
        return view('receipt_history.index');
    }

    public function getTable($status)
    {
        $data = ReceiptHeader::where('process_id', '!=', '0')
            ->where('send_status', '=', $status);
        return DataTables::of($data)->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $company = Session::get('companyCd');
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
            if (!empty ($dt)) {
                for ($i = 0; $i < count($dt); $i++) {
                    $email = trim(strtolower($dt[$i]['email_addr']));

                    $process_id = $dt[$i]['process_id'];
                    $invoice_detail = ReceiptDetail::where('process_id', '=', $process_id)
                        ->where('email_addr', 'LIKE', "%{$email}%")
                        ->first();
                    $filestatus = $invoice_detail->file_status;
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
                        'send_status' => 'S',
                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                    );

                    $data_hdr_failed = array(
                        'send_status' => 'F',
                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                    );

                    if ($filestatus == null) {
                        $filenames = $dt[$i]['filenames'];

                        $filePath = env('ROOT_RECEIPT_FILE_PATH') . $company . '/' . $filenames;
                        $headers = get_headers($filePath);

                        if ($headers && strpos($headers[0], '200 OK') !== false) {
                            if (str_contains($email, ';')) {
                                // Jika ada tanda titik koma
                                $pecah_email = explode(";", $email);

                                try {
                                    if (!empty ($pecah_email)) {
                                        Mail::to($pecah_email)->send(new ReceiptMail($attr));

                                        ReceiptHeader::where($where)->update($data_hdr_success);

                                        ReceiptLog::where($where)->update($data_log_msg_success);
                                    } else {
                                        ReceiptHeader::where($where)->update($data_hdr_failed);

                                        ReceiptLog::where($where)->update($data_log_msg_not_found);
                                    }
                                } catch (\Exception $e) {
                                    $data_log_msg = array(
                                        'status_code' => '400',
                                        'process_id' => $process_id,
                                        'response_message' => 'Failed to send email: ' . $e->getMessage()
                                    );

                                    ReceiptLog::where($where)->update($data_log_msg);
                                }
                            } else {
                                // Jika tidak ada tanda titik koma
                                try {
                                    if (isset ($email) && !empty ($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($email)->send(new ReceiptMail($attr));

                                        ReceiptHeader::where($where)->update($data_hdr_success);

                                        ReceiptLog::where($where)->update($data_log_msg_success);
                                    } else {
                                        ReceiptHeader::where($where)->update($data_hdr_failed);

                                        ReceiptLog::where($where)->update($data_log_msg_not_found);
                                    }
                                } catch (\Exception $e) {
                                    $data_log_msg = array(
                                        'status_code' => '400',
                                        'process_id' => $process_id,
                                        'response_message' => 'Failed to send email: ' . $e->getMessage()
                                    );

                                    ReceiptLog::where($where)->update($data_log_msg);
                                }
                            }

                            $response = array(
                                "Error" => false,
                                "Pesan" => "Email send successfully"
                            );
                        } else {
                            // File does not exist.
                            $response = array(
                                "Error" => true,
                                "Pesan" => "Unable to process email, because the file does not exist"
                            );
                        }
                    } else {
                        $filenames = $dt[$i]['file_names_sign'];

                        $filePath = env('ROOT_SIGNED_FILE_PATH') . $company . '/' . $filenames;
                        $headers = get_headers($filePath);

                        if ($headers && strpos($headers[0], '200 OK') !== false) {
                            if (str_contains($email, ';')) {
                                // Jika ada tanda titik koma
                                $pecah_email = explode(";", $email);

                                try {
                                    if (!empty ($pecah_email)) {
                                        Mail::to($pecah_email)->send(new ReceiptMail($attr));

                                        ReceiptHeader::where($where)->update($data_hdr_success);

                                        ReceiptLog::where($where)->update($data_log_msg_success);
                                    } else {
                                        ReceiptHeader::where($where)->update($data_hdr_failed);

                                        ReceiptLog::where($where)->update($data_log_msg_not_found);
                                    }
                                } catch (\Exception $e) {
                                    $data_log_msg = array(
                                        'status_code' => '400',
                                        'process_id' => $process_id,
                                        'response_message' => 'Failed to send email: ' . $e->getMessage()
                                    );

                                    ReceiptLog::where($where)->update($data_log_msg);
                                }
                            } else {
                                // Jika tidak ada tanda titik koma
                                try {
                                    if (isset ($email) && !empty ($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($email)->send(new ReceiptMail($attr));

                                        ReceiptHeader::where($where)->update($data_hdr_success);

                                        ReceiptLog::where($where)->update($data_log_msg_success);
                                    } else {
                                        ReceiptHeader::where($where)->update($data_hdr_failed);

                                        ReceiptLog::where($where)->update($data_log_msg_not_found);
                                    }
                                } catch (\Exception $e) {
                                    $data_log_msg = array(
                                        'status_code' => '400',
                                        'process_id' => $process_id,
                                        'response_message' => 'Failed to send email: ' . $e->getMessage()
                                    );

                                    ReceiptLog::where($where)->update($data_log_msg);
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
        $company = Session::get('companyCd');

        if ($param['file_status'] == null) {
            $data = array(
                'debtor_acct' => $param['debtor_acct'],
                'debtor_name' => $param['debtor_name'],
                'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $param['gen_date'])->format('d/m/Y'),
                'filestatus' => $param['file_status'],
                'filenames' => $param['filenames'],
                'company' => $company,
            );
        } else {
            $data = array(
                'debtor_acct' => $param['debtor_acct'],
                'debtor_name' => $param['debtor_name'],
                'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $param['gen_date'])->format('d/m/Y'),
                'filestatus' => $param['file_status'],
                'filenames' => $param['file_names_sign'],
                'company' => $company,
            );
        }

        return $data;
    }

    public function show($process_id, $email_addr)
    {
        $data = ReceiptDetail::where('process_id', '=', $process_id)
            ->where('email_addr', 'LIKE', "%{$email_addr}%")
            ->get();
        return response()->json($data);
    }
}
