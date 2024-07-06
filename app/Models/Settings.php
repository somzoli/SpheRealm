<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $primaryKey = 'option';
    protected $guarded = [];
    public $incrementing = false;
}
