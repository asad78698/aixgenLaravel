<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficData extends Model
{
    use HasFactory;

    protected $table = 'trafficdata';

    public function social()
    {
        return $this->hasOne(Social::class, 'trafficdata_id');
    }
}
