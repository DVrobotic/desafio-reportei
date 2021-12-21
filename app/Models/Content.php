<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['file_path'];

    public function box(){
        return $this->belongsTo(Box::class, 'box_id');
    }

    use HasFactory;
}
