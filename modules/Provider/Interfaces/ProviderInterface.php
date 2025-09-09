<?php

namespace Modules\Provider\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\Provider\Entities\Provider;

interface ProviderInterface {
    public function all(): Collection;
    public function findById(int $id): ?Provider;
    public function create(array $attributes): Provider;
}


