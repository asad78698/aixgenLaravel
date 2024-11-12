<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'student';

    public function city(){

        return $this->belongsTo(City::class, 'city_id');
    }

    public function country(){
    
        return $this->belongsTo(Country::class, 'country_id');

    }

    public function course(){

        return $this->belongsTo(Course::class, 'course_id');
    }
}
