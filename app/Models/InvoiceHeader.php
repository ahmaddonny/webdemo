<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHeader extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'mgr.ar_email_inv';

    public $timestamps = false;

    protected $fillable = [
        'entity_cd',
        'entity_name',
        'project_no',
        'project_name',
        'debtor_acct',
        'debtor_name',
        'email_addr',
        'gen_date',
        'doc_no',
        'descs',
        'status_process',
        'process_id',
        'send_status',
        'send_date',
        'audit_user',
        'audit_date'
    ];
}
