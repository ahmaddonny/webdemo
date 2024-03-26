<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;

use App\Mail\InvoiceMail;

use App\Models\InvoiceView;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoiceLog;
use App\Models\ArLedger;
use App\Models\ArMailLog;
use App\Models\Admin\EmailConfiguration;

class InvoiceMailController extends Controller
{
    public function index()
    {
        return view('invoice.index');
    }

    public function getTable(Request $request)
    {
        $data = InvoiceView::all();
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
            if (!empty ($dt)) {
                for ($i = 0; $i < count($dt); $i++) {
                    $email = trim(strtolower($dt[$i]['email_addr']));
                    $process_id = Str::random(8);
                    $filenames = $dt[$i]['filenames'];

                    $attr = $this->attributeEmail($dt[$i]);

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

                    $data_update = array(
                        'process_id' => $process_id
                    );

                    $criteria_pb = array(
                        'entity_cd' => $dt[$i]['entity_cd'],
                        'project_no' => $dt[$i]['project_no'],
                        'debtor_acct' => $dt[$i]['debtor_acct'],
                        'doc_no' => $dt[$i]['doc_no'],
                    );

                    $where = array(
                        'entity_cd' => $dt[$i]['entity_cd'],
                        'project_no' => $dt[$i]['project_no'],
                        'debtor_acct' => $dt[$i]['debtor_acct'],
                        'doc_no' => $dt[$i]['doc_no'],
                        'email_addr' => $dt[$i]['email_addr'],
                        'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $dt[$i]['gen_date'])->format('d M Y H:i:s'),
                        'process_id' => '0'
                    );

                    $filePath = env('ROOT_INVOICE_FILE_PATH') . 'invoice/' . $filenames;
                    $headers = get_headers($filePath);

                    if ($headers && strpos($headers[0], '200 OK') !== false) {
                        if (str_contains($email, ';')) {
                            // Jika ada tanda titik koma
                            $pecah_email = explode(";", $email);

                            foreach ($pecah_email as $emails) {
                                $data_hdr_success = array(
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
                                    'status_process' => 'S',
                                    'process_id' => $process_id,
                                    'send_status' => 'S',
                                    'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                    'audit_user' => 'mgr',
                                    'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                                );

                                $data_hdr_failed = array(
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
                                    'status_process' => 'S',
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
                                    if (!empty ($emails)) {
                                        Mail::to($emails)->send(new InvoiceMail($attr));

                                        $invoice_header = InvoiceHeader::create($data_hdr_success);

                                        if ($invoice_header) {
                                            InvoiceLog::create($data_log_msg_success);

                                            ArLedger::where($criteria_pb)->update($data_pb_success);
                                        } else {
                                            $response = array(
                                                "Error" => false,
                                                "Pesan" => $invoice_header
                                            );
                                        }
                                    } else {
                                        $invoice_header = InvoiceHeader::create($data_hdr_failed);

                                        if ($invoice_header) {
                                            InvoiceLog::create($data_log_msg_not_found);

                                            ArLedger::where($criteria_pb)->update($data_pb_failed);
                                        } else {
                                            $response = array(
                                                "Error" => false,
                                                "Pesan" => $invoice_header
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

                                    $invoice_header = InvoiceHeader::create($data_hdr_failed);

                                    if ($invoice_header) {
                                        InvoiceLog::create($data_log_msg);

                                        ArLedger::where($criteria_pb)->update($data_pb_failed);
                                    } else {
                                        $response = array(
                                            "Error" => false,
                                            "Pesan" => $invoice_header
                                        );
                                    }
                                }
                            }
                        } else {
                            // Jika tidak ada tanda titik koma
                            $data_hdr_success = array(
                                'entity_cd' => $dt[$i]['entity_cd'],
                                'entity_name' => $dt[$i]['entity_name'],
                                'project_no' => $dt[$i]['project_no'],
                                'project_name' => $dt[$i]['project_name'],
                                'debtor_acct' => $dt[$i]['debtor_acct'],
                                'debtor_name' => $dt[$i]['debtor_name'],
                                'email_addr' => $dt[$i]['email_addr'],
                                'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $dt[$i]['gen_date'])->format('d M Y H:i:s'),
                                'doc_no' => $dt[$i]['doc_no'],
                                'descs' => $dt[$i]['descs'],
                                'status_process' => 'S',
                                'process_id' => $process_id,
                                'send_status' => 'S',
                                'send_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s'),
                                'audit_user' => 'mgr',
                                'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                            );

                            $data_hdr_failed = array(
                                'entity_cd' => $dt[$i]['entity_cd'],
                                'entity_name' => $dt[$i]['entity_name'],
                                'project_no' => $dt[$i]['project_no'],
                                'project_name' => $dt[$i]['project_name'],
                                'debtor_acct' => $dt[$i]['debtor_acct'],
                                'debtor_name' => $dt[$i]['debtor_name'],
                                'email_addr' => $dt[$i]['email_addr'],
                                'gen_date' => Carbon::createFromFormat('Y-m-d H:i:s.u', $dt[$i]['gen_date'])->format('d M Y H:i:s'),
                                'doc_no' => $dt[$i]['doc_no'],
                                'descs' => $dt[$i]['descs'],
                                'status_process' => 'S',
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
                                if (isset ($email) && !empty ($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    Mail::to($email)->send(new InvoiceMail($attr));

                                    $invoice_header = InvoiceHeader::create($data_hdr_success);

                                    if ($invoice_header) {
                                        InvoiceLog::create($data_log_msg_success);

                                        ArLedger::where($criteria_pb)->update($data_pb_success);
                                    } else {
                                        $response = array(
                                            "Error" => false,
                                            "Pesan" => $invoice_header
                                        );
                                    }
                                } else {
                                    $invoice_header = InvoiceHeader::create($data_hdr_failed);

                                    if ($invoice_header) {
                                        InvoiceLog::create($data_log_msg_not_found);

                                        ArLedger::where($criteria_pb)->update($data_pb_failed);
                                    } else {
                                        $response = array(
                                            "Error" => false,
                                            "Pesan" => $invoice_header
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

                                $invoice_header = InvoiceHeader::create($data_hdr_failed);

                                if ($invoice_header) {
                                    InvoiceLog::create($data_log_msg);

                                    ArLedger::where($criteria_pb)->update($data_pb_failed);
                                } else {
                                    $response = array(
                                        "Error" => false,
                                        "Pesan" => $invoice_header
                                    );
                                }
                            }
                        }

                        InvoiceDetail::where($where)->update($data_update);

                        ArMailLog::create([
                            'entity_cd' => $dt[$i]['entity_cd'],
                            'project_no' => $dt[$i]['project_no'],
                            'debtor_acct' => $dt[$i]['debtor_acct'],
                            'doc_no' => $dt[$i]['doc_no'],
                            'audit_user' => 'mgr',
                            'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
                        ]);

                        // MOVING FILE AFTER SUCCESS SEND EMAIL@S
                        $ftpServer = env('FTP_INVOICE_SERVER');
                        $ftpUser = env('FTP_INVOICE_USER');
                        $ftpPassword = env('FTP_INVOICE_PASSWORD');

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
                            // get contents of the invoice directory
                            $contents_invoice = ftp_nlist($ftp, './invoice/');
                            $remoteFilePathInvoice = './invoice/' . $filenames;

                            if (in_array($remoteFilePathInvoice, $contents_invoice)) {
                                // File exists
                                $localFilePathInvoice = storage_path('app/public/invoice/' . $filenames);

                                // download temprory directory in storage
                                if (ftp_get($ftp, $localFilePathInvoice, $remoteFilePathInvoice, FTP_BINARY)) {
                                    // move/download file to server folder history
                                    $disk = Storage::build([
                                        'driver' => 'ftp',
                                        'host' => env('FTP_INVOICE_SERVER'),
                                        'username' => env('FTP_INVOICE_USER'),
                                        'password' => env('FTP_INVOICE_PASSWORD'),
                                        'root' => '/invoice/HISTORY_INVOICE/',
                                    ]);

                                    $disk->put($filenames, fopen($localFilePathInvoice, 'r+'));

                                    // delete file in server folder
                                    ftp_delete($ftp, $remoteFilePathInvoice);

                                    // delete file in temporary folder
                                    if (File::exists($localFilePathInvoice)) {
                                        File::delete($localFilePathInvoice);
                                    }
                                } else {
                                    // Handle download failure
                                    $response = array(
                                        "Error" => true,
                                        "Pesan" => "There was an error while downloading " . $localFilePathInvoice
                                    );
                                }
                            } else {
                                // File does not exist.
                                $response = array(
                                    "Error" => true,
                                    "Pesan" => "File does not exist in folder invoice"
                                );
                            }
                        }

                        // close the connection
                        ftp_close($ftp);

                        // MOVING FILE AFTER SUCCESS SEND EMAIL@E

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
            [$param['entity_cd'], $param['project_no'], $param['debtor_acct'], $param['doc_no'], '']
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

    public function storeWA(Request $request)
    {
        $response = Http::post(
            'http://api.ifca.co.id/api/send_wa',
            [
                "debtor_name" => "Aditya Parwito",
                "debtor_phone" => "08112777873",
                "company" => "PT. IFCA Property365 Indonesia",
                "no_invoice" => "INV-CKFJWG-02-2024-0q8s1Paf",
                "debtor_month" => "02-02-2024",
                "amount" => "55000",
                "item_descs" => "e-Meterai",
                "due_date" => "02-03-2024",
                "inv_detail" => "https://get.sandbox.paper.id/c4J8GSh",
                "email_company" => "it@ifca.co.id",
                "phone_company" => "021-8282455"
            ]
        );

        $statusCode = $response->status();

        if ($statusCode == 200) {
            $callback = array(
                "Error" => false,
                "Pesan" => "WhatsApp send successfully"
            );
        } else {
            $callback = array(
                "Error" => false,
                "Pesan" => "Failed Send to WhatsApp"
            );
        }

        return response()->json($callback);
    }

    public function show($doc_no)
    {
        $data = InvoiceDetail::where('process_id', '=', '0')
            ->where('doc_no', '=', $doc_no)
            ->get();
        return response()->json($data);
    }

    public function destroy(Request $request)
    {
        $data = $request->all();

        $criteria = array(
            'process_id' => '0',
            'doc_no' => $data['doc_no']
        );

        $invoice_detail = InvoiceDetail::where($criteria)->first();

        if (!is_null($invoice_detail)) {
            $filenames = $invoice_detail->filenames;

            $ftpServer = env('FTP_INVOICE_SERVER');
            $ftpUser = env('FTP_INVOICE_USER');
            $ftpPassword = env('FTP_INVOICE_PASSWORD');

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
                $contents = ftp_nlist($ftp, './invoice/');
                $remoteFilePath = './invoice/' . $filenames;

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

            $invoice_delete = InvoiceDetail::where($criteria)->delete();

            if ($invoice_delete) {
                $response = array(
                    "Error" => false,
                    "Pesan" => "Deleted Successfully"
                );
            } else {
                $response = array(
                    "Error" => true,
                    "Pesan" => $invoice_delete
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
