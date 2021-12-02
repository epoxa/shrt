<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}
