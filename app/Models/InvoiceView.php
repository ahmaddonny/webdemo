<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceView extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'mgr.v_ar_email_inv';
}
