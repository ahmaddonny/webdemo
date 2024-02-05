<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
    use HasFactory;

    protected $connection = 'DBLIVE';

    protected $table = 'mgr.ar_email_or_dtl';

    public $timestamps = false;

    protected $fillable = [
        'process_id'
    ];
}
