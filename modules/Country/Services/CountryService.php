<?php

namespace modules\Country\Services;

use Illuminate\Database\Eloquent\Collection;
use modules\Country\Entities\Country;
use modules\Country\Interfaces\CountryInterface;

class CountryService
{
    private CountryInterface $countryRepository;

    public function __construct(CountryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function list(): Collection
    {
        return $this->countryRepository->all();
    }

    public function show(int $id): ?Country
    {
        return $this->countryRepository->findById($id);
    }

    public function showByCode(string $code): ?Country
    {
        return $this->countryRepository->findByCode($code);
    }
}
