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


    public function getDynamicMergeTime(DateTime  $lowerLimit = null, DateTime  $higherLimit = null){
        $lowerLimit = $lowerLimit->getTimestamp() ?? strtotime("0000-00-00 00:00:00");
        $higherLimit = $higherLimit->getTimestamp() ?? strtotime("now");
        $start = ($this->created_at != 0 && $this->created_at > $lowerLimit  ? $this->created_at : $lowerLimit);
        $end = ($this->closed_at != 0 && $this->closed_at < $higherLimit  ? $this->closed_at : $higherLimit);
        return $end - $start;
    }

    public static function scopeValidPrsForTimespan(Builder $query, DateTime  $lowerLimit = null, DateTime  $higherLimit = null){
        $lowerLimit = $lowerLimit->getTimestamp() ?? strtotime("0000-00-00 00:00:00");
        $higherLimit = $higherLimit->getTimestamp() ?? strtotime("now");
        return  $query->where('created_at', '<=', $higherLimit)
                ->where(function($query) use ($lowerLimit)
                {
                    $query
                        ->where('open', true)
                        ->orWhere('closed_at', '>=', $lowerLimit);
                })
            ->orderBy('owner', 'asc');
    }

    public static function scopeOnlyWithin($query,DateTime $lowerLimit = null, DateTime  $higherLimit = null){
        $lowerLimit = $lowerLimit->getTimestamp() ?? strtotime("0000-00-00 00:00:00");
        $higherLimit = $higherLimit->getTimestamp() ?? strtotime("now");
        return $query->where('created_at', '>=', $lowerLimit)->where('created_at', '<=', $higherLimit);
    }

    public static function scopeNoForgotten($query,DateTime $lowerLimit = null, DateTime  $higherLimit = null){
        $query = $query->ValidPrsForTimespan($lowerLimit, $higherLimit);
        $lowerLimit = $lowerLimit->getTimestamp() ?? strtotime("0000-00-00 00:00:00");
        $higherLimit = $higherLimit->getTimestamp() ?? strtotime("now");
        return $query->where(function ($query) use ($lowerLimit){
            return $query->where('open', true)->where('created_at', '>=', $lowerLimit);
        });
    }

}
