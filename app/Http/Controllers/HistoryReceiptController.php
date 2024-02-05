<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
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
        $company = 'CKFJWG';
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
                        'status_process' => 'S',
                        'send_status' => 'S',
                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                    );

                    $data_hdr_failed = array(
                        'status_process' => 'S',
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

                                foreach ($pecah_email as $emails) {
                                    $data_hdr_success_no_stamp = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $emails,
                                        'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $dt[$i]['gen_date'])->format('d M Y H:i:s'),
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'descs' => $dt[$i]['descs'],
                                        'status_process' => 'N',
                                        'process_id' => $process_id,
                                        'send_status' => 'S',
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    $data_hdr_failed_no_stamp = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $emails,
                                        'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $dt[$i]['gen_date'])->format('d M Y H:i:s'),
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'descs' => $dt[$i]['descs'],
                                        'status_process' => 'N',
                                        'process_id' => $process_id,
                                        'send_status' => 'F',
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    $data_log_msg_success = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $emails,
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'status_code' => '200',
                                        'process_id' => $process_id,
                                        'response_message' => 'Email sent successfully to: ' . $emails,
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    $data_log_msg_not_found = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $emails,
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'status_code' => '404',
                                        'process_id' => $process_id,
                                        'response_message' => 'No email address was given.',
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    try {
                                        if (!empty($emails)) {
                                            Mail::to($emails)->send(new ReceiptMail($attr));

                                            $receipt_header = ReceiptHeader::create($data_hdr_success_no_stamp);

                                            if ($receipt_header) {
                                                ReceiptLog::create($data_log_msg_success);
                                            } else {
                                                $response = array(
                                                    "Error" => false,
                                                    "Pesan" => $receipt_header
                                                );
                                            }
                                        } else {
                                            $receipt_header = ReceiptHeader::create($data_hdr_failed_no_stamp);

                                            if ($receipt_header) {
                                                ReceiptLog::create($data_log_msg_not_found);
                                            } else {
                                                $response = array(
                                                    "Error" => false,
                                                    "Pesan" => $receipt_header
                                                );
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        $data_log_msg = array(
                                            'entity_cd' => $dt[$i]['entity_cd'],
                                            'entity_name' => $dt[$i]['entity_name'],
                                            'project_no' => $dt[$i]['project_no'],
                                            'project_name' => $dt[$i]['project_name'],
                                            'debtor_acct' => $dt[$i]['debtor_acct'],
                                            'debtor_name' => $dt[$i]['debtor_name'],
                                            'email_addr' => $emails,
                                            'doc_no' => $dt[$i]['doc_no'],
                                            'status_code' => '400',
                                            'process_id' => $process_id,
                                            'response_message' => 'Failed to send email: ' . $e->getMessage(),
                                            'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                            'audit_user' => 'mgr',
                                            'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                        );

                                        $receipt_header = ReceiptHeader::create($data_hdr_failed_no_stamp);

                                        if ($receipt_header) {
                                            ReceiptLog::create($data_log_msg);
                                        } else {
                                            $response = array(
                                                "Error" => false,
                                                "Pesan" => $receipt_header
                                            );
                                        }
                                    }
                                }
                            } else {
                                // Jika tidak ada tanda titik koma
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

                                try {
                                    if (isset($email) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($email)->send(new ReceiptMail($attr));

                                        $receipt_header = ReceiptHeader::create($data_hdr_success_no_stamp);

                                        if ($receipt_header) {
                                            ReceiptLog::create($data_log_msg_success);
                                        } else {
                                            $response = array(
                                                "Error" => false,
                                                "Pesan" => $receipt_header
                                            );
                                        }
                                    } else {
                                        $receipt_header = ReceiptHeader::create($data_hdr_failed_no_stamp);

                                        if ($receipt_header) {
                                            ReceiptLog::create($data_log_msg_not_found);
                                        } else {
                                            $response = array(
                                                "Error" => false,
                                                "Pesan" => $receipt_header
                                            );
                                        }
                                    }
                                } catch (\Exception $e) {
                                    $data_log_msg = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $dt[$i]['email_addr'],
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'status_code' => '400',
                                        'process_id' => $process_id,
                                        'response_message' => 'Failed to send email: ' . $e->getMessage(),
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    $receipt_header = ReceiptHeader::create($data_hdr_failed_no_stamp);

                                    if ($receipt_header) {
                                        ReceiptLog::create($data_log_msg);
                                    } else {
                                        $response = array(
                                            "Error" => false,
                                            "Pesan" => $receipt_header
                                        );
                                    }
                                }
                            }

                            ReceiptDetail::where($where)->update($data_update);

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

                                foreach ($pecah_email as $emails) {
                                    $data_hdr_success = array(
                                        'send_status' => 'S',
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    $data_hdr_failed = array(
                                        'send_status' => 'F',
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    $data_log_msg_success = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $emails,
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'status_code' => '200',
                                        'process_id' => $process_id,
                                        'response_message' => 'Email sent successfully to: ' . $emails,
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    $data_log_msg_not_found = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $emails,
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'status_code' => '404',
                                        'process_id' => $process_id,
                                        'response_message' => 'No email address was given.',
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    try {
                                        if (!empty($emails)) {
                                            Mail::to($emails)->send(new ReceiptMail($attr));

                                            ReceiptHeader::where($where)->update($data_hdr_success);

                                            ReceiptLog::create($data_log_msg_success);
                                        } else {
                                            ReceiptHeader::where($where)->update($data_hdr_failed);

                                            ReceiptLog::create($data_log_msg_not_found);
                                        }
                                    } catch (\Exception $e) {
                                        $data_log_msg = array(
                                            'entity_cd' => $dt[$i]['entity_cd'],
                                            'entity_name' => $dt[$i]['entity_name'],
                                            'project_no' => $dt[$i]['project_no'],
                                            'project_name' => $dt[$i]['project_name'],
                                            'debtor_acct' => $dt[$i]['debtor_acct'],
                                            'debtor_name' => $dt[$i]['debtor_name'],
                                            'email_addr' => $emails,
                                            'doc_no' => $dt[$i]['doc_no'],
                                            'status_code' => '400',
                                            'process_id' => $process_id,
                                            'response_message' => 'Failed to send email: ' . $e->getMessage(),
                                            'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                            'audit_user' => 'mgr',
                                            'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                        );

                                        ReceiptHeader::where($where)->update($data_hdr_failed);

                                        ReceiptLog::create($data_log_msg);
                                    }
                                }
                            } else {
                                // Jika tidak ada tanda titik koma
                                $data_hdr_success = array(
                                    'send_status' => 'S',
                                    'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                );

                                $data_hdr_failed = array(
                                    'send_status' => 'F',
                                    'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                );

                                $data_log_msg_success = array(
                                    'entity_cd' => $dt[$i]['entity_cd'],
                                    'entity_name' => $dt[$i]['entity_name'],
                                    'project_no' => $dt[$i]['project_no'],
                                    'project_name' => $dt[$i]['project_name'],
                                    'debtor_acct' => $dt[$i]['debtor_acct'],
                                    'debtor_name' => $dt[$i]['debtor_name'],
                                    'email_addr' => $dt[$i]['email_addr'],
                                    'doc_no' => $dt[$i]['doc_no'],
                                    'status_code' => '200',
                                    'process_id' => $process_id,
                                    'response_message' => 'Email sent successfully to: ' . $email,
                                    'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                    'audit_user' => 'mgr',
                                    'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                );

                                $data_log_msg_not_found = array(
                                    'entity_cd' => $dt[$i]['entity_cd'],
                                    'entity_name' => $dt[$i]['entity_name'],
                                    'project_no' => $dt[$i]['project_no'],
                                    'project_name' => $dt[$i]['project_name'],
                                    'debtor_acct' => $dt[$i]['debtor_acct'],
                                    'debtor_name' => $dt[$i]['debtor_name'],
                                    'email_addr' => $dt[$i]['email_addr'],
                                    'doc_no' => $dt[$i]['doc_no'],
                                    'status_code' => '404',
                                    'process_id' => $process_id,
                                    'response_message' => 'No email address was given.',
                                    'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                    'audit_user' => 'mgr',
                                    'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                );

                                try {
                                    if (isset($email) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        Mail::to($email)->send(new ReceiptMail($attr));

                                        ReceiptHeader::where($where)->update($data_hdr_success);

                                        ReceiptLog::create($data_log_msg_success);
                                    } else {
                                        ReceiptHeader::where($where)->update($data_hdr_failed);

                                        ReceiptLog::create($data_log_msg_not_found);
                                    }
                                } catch (\Exception $e) {
                                    $data_log_msg = array(
                                        'entity_cd' => $dt[$i]['entity_cd'],
                                        'entity_name' => $dt[$i]['entity_name'],
                                        'project_no' => $dt[$i]['project_no'],
                                        'project_name' => $dt[$i]['project_name'],
                                        'debtor_acct' => $dt[$i]['debtor_acct'],
                                        'debtor_name' => $dt[$i]['debtor_name'],
                                        'email_addr' => $dt[$i]['email_addr'],
                                        'doc_no' => $dt[$i]['doc_no'],
                                        'status_code' => '400',
                                        'process_id' => $process_id,
                                        'response_message' => 'Failed to send email: ' . $e->getMessage(),
                                        'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                        'audit_user' => 'mgr',
                                        'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                    );

                                    ReceiptHeader::where($where)->update($data_hdr_failed);

                                    ReceiptLog::create($data_log_msg);
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
        $company = 'CKFJWG';

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
