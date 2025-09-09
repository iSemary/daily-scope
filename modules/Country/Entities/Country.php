<?php

namespace Modules\Country\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public static function getCountryIdByCode(string $countryCode): int
    {
        $country = self::select('id')->whereCode($countryCode)->first();
        if (!$country) {
            $country = self::create([
                'name' => $countryCode,
                'code' => $countryCode,
            ]);
        }
        return $country->id;
    }

    public function users()
    {
        return $this->hasMany(\Modules\User\Entities\User::class);
    }

    public function sources()
    {
        return $this->hasMany(\Modules\Source\Entities\Source::class);
    }

    public function articles()
    {
        return $this->hasMany(\Modules\Article\Entities\Article::class);
    }
}
