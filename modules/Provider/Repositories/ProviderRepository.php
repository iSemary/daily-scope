<?php

namespace Modules\Provider\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Provider\Entities\Provider;
use Modules\Provider\Interfaces\ProviderInterface;

class ProviderRepository implements ProviderInterface {
    public function all(): Collection {
        return Provider::select(['id', 'name', 'class_name', 'end_point', 'fetched_at'])
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?Provider {
        return Provider::select(['id', 'name', 'class_name', 'end_point', 'fetched_at'])->find($id);
    }

    public function create(array $attributes): Provider {
        return Provider::create($attributes);
    }
}


