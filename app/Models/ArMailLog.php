<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArMailLog extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'mgr.ar_mail_log';

    public $timestamps = false;

    protected $fillable = [
        'entity_cd',
        'project_no',
        'debtor_acct',
        'doc_no',
        'audit_user',
        'audit_date'
    ];
}
