<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTest extends Model
{
    use HasFactory;

    protected $table = 'salestest';

    public function social()
    {
        return $this->hasOne(Social::class, 'salestest_id');
    }


}
