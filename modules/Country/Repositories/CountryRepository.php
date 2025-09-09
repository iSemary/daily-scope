<?php

namespace Modules\Country\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Country\Entities\Country;
use Modules\Country\Interfaces\CountryInterface;

class CountryRepository implements CountryInterface
{
    public function all(): Collection
    {
        return Country::select(['id', 'name', 'code'])->orderBy("name")->get();
    }

    public function findById(int $id): ?Country
    {
        return Country::select(['id', 'name', 'code'])->find($id);
    }

    public function findByCode(string $code): ?Country
    {
        return Country::select(['id', 'name', 'code'])->where('code', $code)->first();
    }
}
