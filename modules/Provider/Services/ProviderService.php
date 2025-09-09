<?php

namespace modules\Provider\Services;

use App\Services\ScrapNews;
use Illuminate\Database\Eloquent\Collection;
use modules\Provider\Entities\Provider;
use modules\Provider\Interfaces\ProviderInterface;

class ProviderService {
    private ProviderInterface $providers;
    private ScrapNews $scrapNews;

    public function __construct(ProviderInterface $providers, ScrapNews $scrapNews) {
        $this->providers = $providers;
        $this->scrapNews = $scrapNews;
    }

    public function list(): Collection {
        return $this->providers->all();
    }

    public function show(int $id): ?Provider {
        return $this->providers->findById($id);
    }

    public function register(array $data): Provider {
        return $this->providers->create($data);
    }

    public function sync(): void {
        $this->scrapNews->run();
    }
}


