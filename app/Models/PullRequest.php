<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Livewire\str;

class PullRequest extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function getDynamicMergeTime(int  $start = null, int  $end = null){
        $start = ($this->created_at != 0 && $this->created_at > $start  ? $this->created_at : $start);
        $end = ($this->closed_at != 0 && $this->closed_at < $end  ? $this->closed_at : $end);
        return $end - $start;
    }

    //returns the prs that have an intersection with the timeline passed,
    //first it checks if they were created before the $end of the timeline
    //then it checks if wasn't closed before the $start of the timeline
    //if both condition are true, then its an intersection
    public static function scopeIntersection($query, int $start, int $end){
        return $query
            ->where('created_at', '<' , $end) #pr created before the timeline starts
            ->notClosedBefore($start); #and is not closed before the start
    }

    //get all prs that are not closed before the time passed, if its open it should be included too
    public static function scopeNotClosedBefore($query, int $start)
    {
        return $query
            ->where('open', true) #if its open, it has no closing time, therefore most be closed after
            ->orWhere(function($query) use ($start){ #closure to make sure only closed ones are being counted in
                return $query
                    ->where('open', false)
                    ->where('closed_at', '>', $start); #if it closed after the start and it started before, there must be an intersection
            });
    }

    public static function scopeOnlyWithin($query, int $start, int $end){
        return $query->where('created_at', ">=", $start)->where("created_at", "<=", $end);
    }

    //returns all closed prs relatively to the max time passed
    public static function scopeClosed($query, int $end){
        return $query
            ->where('open', false)
            ->where('closed_at', '<=', $end);
    }

    public static function scopeOr($query, callable $closure, array $args){
        return $query->orWhere(fn($query) => $query->closure(...$args));
    }
}
