<?php

namespace App\Models;

use Carbon\Traits\Date;
use DateInterval;
use DatePeriod;
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
        $end = (!$this->isOpen() ? $this->closed_at : $end);
        return $end - $start;
    }

    //returns the prs that have an intersection with the timeline passed,
    //first it checks if they were created before the $end of the timeline
    //then it checks if wasn't closed before the $start of the timeline
    //if both condition are true, then its an intersection
    public static function scopeIntersection($query, int $start, int $end){
        return $query
            ->where('created_at', '<' , $end) #pr created before the timeline starts
            ->notClosedBefore($start, $end); #and is not closed before the start
    }

    //get all prs that are not closed before the time passed, if its open it should be included too
    public static function scopeNotClosedBefore($query, int $start, int $end)
    {
        return $query
            ->open($end, true) #if its open, it has no closing time, therefore most be closed after
            ->orWhere(function($query) use ($start, $end){ #closure to make sure only closed ones are being counted in
                return $query
                    ->open($end, false)
                    ->where('closed_at', '>', $start); #if it closed after the start and it started before, there must be an intersection
            });
    }

    public static function scopeOnlyWithin($query, int $start, int $end){
        return $query->where('created_at', ">=", $start)->where("created_at", "<=", $end);
    }

    public static function scopeOr($query, callable $closure, array $args){
        return $query->orWhere(fn($query) => $query->closure(...$args));
    }

    public function isOpen(Datetime|int $end = null) : bool{
        $end = $end ?? strtotime('now');
        if($end instanceof DateTime){
            $end = $end->getTimestamp();
        }
        return $this->closed_at == 0 || $this->closed_at >= $end;

    }

    public static function scopeOpen($query, int $end, bool $open){
        if($open){
            return $query->where("closed_at", '=', 0)->orWhere('closed_at', '>=', $end);
        }
        return $query->where("closed_at", '!=', 0)->where('closed_at', '<=', $end);
    }


    public function dateMatches(Datetime $date, string $format){
        return date($format, $this->created_at) == $date->format($format);
    }

    public static function getFormat(DatePeriod $period){
        if($period->getDateInterval()->y){
            return 'Y';
        } else if($period->getDateInterval()->m){
            return 'm-Y';
        } else{
            return 'd-m-Y';
        }
    }
}
