<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public $timestamps = false;

    public static function getData($key) {
        return static::where('key', $key)->first();
    }

    public static function setData($key, $value) {
        $setting = static::getData($key);
        if ($setting === null) {
            return static::create(['key' => $key, 'value' => $value]);
        }

        return static::where('key', $key)->update(['value' => $value]);
    }
}
