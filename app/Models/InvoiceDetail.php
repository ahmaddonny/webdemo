<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'mgr.ar_email_inv_dtl';

    public $timestamps = false;

    protected $fillable = [
        'process_id'
    ];
}
