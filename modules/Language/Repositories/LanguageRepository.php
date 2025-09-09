<?php

namespace Modules\Language\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Language\Entities\Language;
use Modules\Language\Interfaces\LanguageInterface;

class LanguageRepository implements LanguageInterface
{
    public function all(): Collection
    {
        return Language::select(['id', 'name', 'code'])->orderBy("name")->get();
    }

    public function findById(int $id): ?Language
    {
        return Language::select(['id', 'name', 'code'])->find($id);
    }

    public function findByCode(string $code): ?Language
    {
        return Language::select(['id', 'name', 'code'])->where('code', $code)->first();
    }
}
