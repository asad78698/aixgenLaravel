<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'city';

    public $updated_at = false;
    public $created_at = false;

    public function student(){

        return $this->hasOne(Student::class,'city_id');
    }


}
