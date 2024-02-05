<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptLog extends Model
{
    use HasFactory;

    protected $connection = 'DBLIVE';

    protected $table = 'mgr.ar_email_or_log_msg';

    public $timestamps = false;

    protected $fillable = [
        'entity_cd',
        'entity_name',
        'project_no',
        'project_name',
        'debtor_acct',
        'debtor_name',
        'email_addr',
        'doc_no',
        'status_code',
        'process_id',
        'response_message',
        'send_date',
        'audit_user',
        'audit_date'
    ];
}
