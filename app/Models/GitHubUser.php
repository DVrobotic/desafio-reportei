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
        'name' => 'array',
        'name_dates' => 'array',
    ];


    public function nameIncluded(string $name){
        return in_array($name, $this->name);
    }

    public static function getNameAssociate($users, string $name){
        return $users->filter(fn($user) => $user->nameIncluded($name))->first();
    }

}
