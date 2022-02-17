<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public static function scopeWithin($query, DateTime|int $start, DateTime|int $end){
        $start = $start instanceof \DateTime ? $start->getTimestamp() : $start;
        $end = $end instanceof \DateTime ? $end->getTimestamp() : $end;
        return $query
            ->where('created_at', ">=", $start)
            ->where('created_at', "<=", $end);

    }

    public static function scopeGroupByAndCount($query, string $parameter){
        return $query->groupBy($parameter)->selectRaw("count(*) as total, {$parameter}");
    }

    public function gitHubUser(){
        return $this->belongsTo(GitHubUser::class, 'fk_owner_id');
    }

}
