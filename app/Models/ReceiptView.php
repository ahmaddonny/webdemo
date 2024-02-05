<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptView extends Model
{
    use HasFactory;

    protected $connection = 'DBLIVE';

    protected $table = 'mgr.v_ar_email_or';
}
