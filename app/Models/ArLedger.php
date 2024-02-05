<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArLedger extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'mgr.ar_ledger';

    public $timestamps = false;

    protected $fillable = [
        'status_email',
        'status_proses',
        'status_gen'
    ];
}
