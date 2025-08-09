<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trackings extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'date_updated',
        'positions'
    ];

    public function getDevice()
    {
        return $this->belongsTo('App\Models\Getgsminfo', 'device_id');
    }

}