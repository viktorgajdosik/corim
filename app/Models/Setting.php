<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key','value'];

    public static function get(string $key, $default = null) {
        $val = static::query()->where('key',$key)->value('value');
        return $val ?? $default;
    }

    public static function set(string $key, $value): void {
        static::updateOrCreate(['key'=>$key], ['value'=>$value]);
    }

    public static function many(array $defaults = []): array {
        $rows = static::all()->pluck('value','key')->toArray();
        return array_merge($defaults, $rows);
    }
}
