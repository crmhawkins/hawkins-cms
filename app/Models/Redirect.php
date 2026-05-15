<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['from_url','to_url','status_code','active','notes'];

    protected $casts = ['active' => 'boolean', 'status_code' => 'integer'];
}
