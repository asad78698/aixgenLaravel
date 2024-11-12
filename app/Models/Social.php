<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    use HasFactory;

    protected $table = 'social';

    public function salesTest()
    {
        return $this->belongsTo(SalesTest::class, 'salestest_id');
    }

    public function trafficData(){

        return $this->belongsTo(TrafficData::class, 'trafficdata_id');
    }
}


// this is how you insert the data in table where you have foreign key of another table

// $social = new App\Models\Social(); $social->facebook = '250'; $social->twitter = '450'; $social->instagram = '550'; $s
// ocial->youtube = '650'; $social->trafficdata_id = App\Models\TrafficData::first()->id; $social->salestest_id = App\Model
// s\SalesTest::first()->id; $social->save();