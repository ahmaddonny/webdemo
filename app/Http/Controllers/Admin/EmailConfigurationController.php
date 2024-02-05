<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Models\Admin\EmailConfiguration;

class EmailConfigurationController extends Controller
{
    public function index()
    {
        return view('admin.email_config.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $driver = $data['driver'];
        $host = $data['host'];
        $port = $data['port'];
        $username = $data['username'];
        $password = $data['password'];
        $passHash = Crypt::encryptString($password);
        $encryption = $data['encryption'];
        $sender_name = $data['sender_name'];
        $sender_email = $data['sender_email'];

        $data_insert = array(
            'driver' => $driver,
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $passHash,
            'encryption' => $encryption,
            'sender_name' => $sender_name,
            'sender_email' => $sender_email,
            'audit_user' => 'mgr',
            'audit_date' => Carbon::now('Asia/Jakarta')->format('d M Y H:i:s')
        );

        $data_email = EmailConfiguration::first();

        if (!is_null($data_email)) {
            $update = EmailConfiguration::where('id', 1)->update($data_insert);

            if ($update) {
                $response = array(
                    "Error" => false,
                    "Pesan" => "Updated Successfully"
                );
            } else {
                $response = array(
                    "Error" => true,
                    "Pesan" => $update
                );
            }
        } else {
            $insert = EmailConfiguration::create($data_insert);

            if ($insert) {
                $response = array(
                    "Error" => false,
                    "Pesan" => "Created Successfully"
                );
            } else {
                $response = array(
                    "Error" => true,
                    "Pesan" => $insert
                );
            }
        }

        return response()->json($response);
    }

    public function show()
    {
        $data_email = EmailConfiguration::first();
        $decryptPass = Crypt::decryptString($data_email->password);

        return response()->json(compact('data_email', 'decryptPass'));
    }
}
