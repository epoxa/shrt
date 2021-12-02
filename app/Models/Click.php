<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $url_id
 * @property int $banner_id
 * @property string $ip
 */
class Click extends Model
{
    use HasFactory;

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

}
