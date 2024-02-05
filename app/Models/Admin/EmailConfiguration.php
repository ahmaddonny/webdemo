<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv';

    protected $table = 'mgr.email_configuration';

    public $timestamps = false;

    protected $fillable = [
        'driver',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'sender_name',
        'sender_email',
        'audit_user',
        'audit_date'
    ];
}
