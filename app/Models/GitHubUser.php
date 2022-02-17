<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class GitHubUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'name' => AsArrayObject::class,
        'name_dates' => AsArrayObject::class,
    ];
}
