<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppConfigurations extends Model
{
    use HasFactory;
    protected $fillable = ['config_key', 'config_value'];
}
