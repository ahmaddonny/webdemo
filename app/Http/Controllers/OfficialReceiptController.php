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

class OfficialReceiptController extends Controller
{
    public function index()
    {
        return view('receipt.index');
    }

    public function getTable()
    {
        // $data = ReceiptView::all();
        $query = "SELECT * FROM mgr.ar_email_or_dtl
            WHERE NOT EXISTS (
                SELECT * FROM mgr.ar_email_or
                    WHERE mgr.ar_email_or.entity_cd = mgr.ar_email_or_dtl.entity_cd
                    AND mgr.ar_email_or.project_no = mgr.ar_email_or_dtl.project_no
                    AND mgr.ar_email_or.debtor_acct = mgr.ar_email_or_dtl.debtor_acct
                    AND mgr.ar_email_or.gen_date = mgr.ar_email_or_dtl.gen_date
                    AND mgr.ar_email_or.process_id = mgr.ar_email_or_dtl.process_id
                    AND mgr.ar_email_or.send_status IN ('S', 'F')
                )
            ORDER BY rowid DESC";
        $data = DB::connection('DBLIVE')->select($query);
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
                    $process_id = Str::random(8);
                    $filestatus = $dt[$i]['file_status'];

                    $attr = $this->attributeEmail($dt[$i]);

                    $data_update = array(
                        'process_id' => $process_id
                    );

                    $where = array(
                        'entity_cd' => $dt[$i]['entity_cd'],
                        'project_no' => $dt[$i]['project_no'],
                        'debtor_acct' => $dt[$i]['debtor_acct'],
                        'doc_no' => $dt[$i]['doc_no'],
                        'email_addr' => $dt[$i]['email_addr'],
                        'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $dt[$i]['gen_date'])->format('d M Y H:i:s'),
                        'process_id' => $dt[$i]['process_id']
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
                                $data_hdr_success_no_stamp = array(
                                    'entity_cd' => $dt[$i]['entity_cd'],
                                    'entity_name' => $dt[$i]['entity_name'],
                                    'project_no' => $dt[$i]['project_no'],
                                    'project_name' => $dt[$i]['project_name'],
                                    'debtor_acct' => $dt[$i]['debtor_acct'],
                                    'debtor_name' => $dt[$i]['debtor_name'],
                                    'email_addr' => $email,
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
                                    'email_addr' => $email,
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

    public function storeStamp(Request $request)
    {
        $data = $request->all();
        $dt = $data['models'];
        $company = 'CKFJWG';

        if (!empty($dt)) {
            $count = count($dt);

            $kuota = Http::get(env('API_GATEWAY') . 'getKuota?company_cd=' . $company);
            $data_kuota = $kuota->json('Data')["saldo"];

            if ($count > $data_kuota) {
                $callback = array(
                    "Error" => true,
                    "Pesan" => "Sorry, unable to process. Because the file you selected exceeded the existing quota."
                );
            } else {
                for ($i = 0; $i < count($dt); $i++) {
                    $process_id = Str::random(8);

                    $filenames = $dt[$i]['filenames'];

                    $response = Http::post(
                        env('API_GATEWAY') . 'stamping-outside/posting',
                        [
                            'fileName' => $filenames,
                            'company_cd' => $company,
                            'stamp_qty' => '1'
                        ]
                    );

                    $statusCode = $response->status();

                    if ($statusCode == 200) {
                        $responseData = $response->json('Result');

                        $data_hdr = array(
                            'entity_cd' => $dt[$i]['entity_cd'],
                            'entity_name' => $dt[$i]['entity_name'],
                            'project_no' => $dt[$i]['project_no'],
                            'project_name' => $dt[$i]['project_name'],
                            'debtor_acct' => $dt[$i]['debtor_acct'],
                            'debtor_name' => $dt[$i]['debtor_name'],
                            'email_addr' => $dt[$i]['email_addr'],
                            'doc_no' => $dt[$i]['doc_no'],
                            'descs' => $dt[$i]['descs'],
                            'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $dt[$i]['gen_date'])->format('d M Y H:i:s'),
                            'status_process' => 'S',
                            'process_id' => $process_id,
                            'audit_user' => 'KALINDO',
                            'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                        );

                        ReceiptHeader::create($data_hdr);

                        $where = array(
                            'filenames' => $filenames
                        );

                        $data_update = array(
                            'process_id' => $process_id,
                            'file_names_sign' => $responseData['file_names_sign'],
                            'file_token' => $responseData['file_token'],
                            'file_serial_number' => $responseData['file_serial_number'],
                            'file_status' => $responseData['file_status']
                        );

                        ReceiptDetail::where($where)->update($data_update);

                        // MOVING FILE AFTER SUCCESS STAMPING@S
                        if ($responseData['file_status'] == 'S') {
                            $ftpServer = env('FTP_RECEIPT_SERVER');
                            $ftpUser = env('FTP_RECEIPT_USER');
                            $ftpPassword = env('FTP_RECEIPT_PASSWORD');

                            $ftp = ftp_connect($ftpServer);

                            // login with username and password
                            $loginResult = ftp_login($ftp, $ftpUser, $ftpPassword);

                            if (!$loginResult) {
                                $callback = array(
                                    "Error" => true,
                                    "Pesan" => "Can't Login FTP Server"
                                );
                            } else {
                                // get contents of the current directory
                                $contents = ftp_nlist($ftp, './UNSIGNED/' . $company);
                                $remoteFilePath = './UNSIGNED/' . $company . '/' . $filenames;

                                if (in_array($remoteFilePath, $contents)) {
                                    // File exists
                                    $localFilePath = storage_path('app/public/receipt/' . $filenames);

                                    // download temprory directory in storage
                                    if (ftp_get($ftp, $localFilePath, $remoteFilePath, FTP_BINARY)) {
                                        // delete file in server folder unsigned
                                        ftp_delete($ftp, $remoteFilePath);

                                        // delete file in temporary folder
                                        if (File::exists($localFilePath)) {
                                            File::delete($localFilePath);
                                        }
                                    } else {
                                        // Handle download failure
                                        $callback = array(
                                            "Error" => true,
                                            "Pesan" => "There was an error while downloading " . $localFilePath
                                        );
                                    }
                                } else {
                                    // File does not exist.
                                    $callback = array(
                                        "Error" => true,
                                        "Pesan" => "File does not exist"
                                    );
                                }
                            }

                            // close the connection
                            ftp_close($ftp);
                        }
                        // MOVING FILE AFTER SUCCESS STAMPING@E
                    } else {
                        $callback = array(
                            "Error" => true,
                            "Pesan" => $response->json("Pesan")
                        );
                    }
                }

                $callback = array(
                    "Error" => false,
                    "Pesan" => $response->json("Pesan")
                );
            }

            return response()->json($callback);
        }
    }

    public function show($doc_no)
    {
        $data = ReceiptDetail::where('process_id', '=', '0')
            ->where('doc_no', '=', $doc_no)
            ->get();
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = $request->all();

        $company = 'CKFJWG';

        $criteria = array(
            'process_id' => '0',
            'doc_no' => $data['doc_no']
        );

        $receipt_detail = ReceiptDetail::where($criteria)->first();

        if (!is_null($receipt_detail)) {
            $filenames = $receipt_detail->filenames;

            $ftpServer = env('FTP_RECEIPT_SERVER');
            $ftpUser = env('FTP_RECEIPT_USER');
            $ftpPassword = env('FTP_RECEIPT_PASSWORD');

            $ftp = ftp_connect($ftpServer);

            // login with username and password
            $loginResult = ftp_login($ftp, $ftpUser, $ftpPassword);

            // turn passive mode on
            ftp_pasv($ftp, true);

            if (!$loginResult) {
                $response = array(
                    "Error" => true,
                    "Pesan" => "Can't Login FTP Server"
                );
            } else {
                // get contents of the current directory
                $contents = ftp_nlist($ftp, './UNSIGNED/' . $company);
                $remoteFilePath = './UNSIGNED/' . $company . '/' . $filenames;

                if (in_array($remoteFilePath, $contents)) {
                    // delete file in server folder
                    ftp_delete($ftp, $remoteFilePath);
                } else {
                    // File does not exist.
                    $response = array(
                        "Error" => true,
                        "Pesan" => "File does not exist"
                    );
                }
            }

            // close the connection
            ftp_close($ftp);

            $receipt_delete = ReceiptDetail::where($criteria)->delete();

            if ($receipt_delete) {
                $response = array(
                    "Error" => false,
                    "Pesan" => "Deleted Successfully"
                );
            } else {
                $response = array(
                    "Error" => true,
                    "Pesan" => $receipt_delete
                );
            }
        } else {
            $response = array(
                "Error" => true,
                "Pesan" => "Data not found."
            );
        }

        return response()->json($response);
    }
}
