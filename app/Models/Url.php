<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

/**
 * @property integer id
 * @property string $original_url
 * @property string $short_url
 * @property boolean $is_commercial
 * @property ?string $valid_until
 */

class Url extends Model
{

    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['original_url','is_commercial','valid_until','short_url'];

    /** @noinspection PhpUnused */
    public function setIsCommercialAttribute($value)
    {
        $this->attributes['is_commercial'] = $value ? 1 : 0;
    }

    public function getTimeRemains(): string
    {
        if (!$this->valid_until) {
            return 'Неограничен';
        } else if (($delta = strtotime($this->valid_until) - time()) < 0) {
            return 'Истек';
        } else {
            $hours = intdiv($delta, 3600);
            if ($hours >= 24) {
                $days = intdiv($hours, 24);
                return "Осталось дней &ndash; $days";
            } else if ($hours) {
                return "Осталось часов &ndash; $hours";
            } else {
                return "Меньше часа";
            }
        }
    }

}
