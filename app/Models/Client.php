<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'ip',
        'description',
        'type',
        'port'
    ];

    public function sshKeys()
    {
        return $this->belongsTo(SshKeys::class);
    }
}
