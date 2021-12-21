<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    protected $fillable = ["id", "name"];

    public function banner(){
        return $this->belongsTo(Contnet::class, 'content_id');
    }

    use HasFactory;
}
