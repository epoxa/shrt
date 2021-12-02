<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $file_name
 */
class Banner extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['file_name'];

    static public function getRandom(): Banner
    {
        $files = glob(public_path() . '/banners/*.jpg');
        $idx = rand(0, count($files) - 1);
        preg_match('#/([^/]+\.jpg)$#', $files[$idx], $a);
        return self::firstOrCreate(['file_name' => $a[1]]);
    }
}
