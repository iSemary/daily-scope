<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ApiVersionServiceProvider::class,
    App\Providers\HomeServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
    Modules\Article\Providers\ArticleModuleServiceProvider::class,
    Modules\Author\Providers\AuthorModuleServiceProvider::class,
    Modules\Category\Providers\CategoryModuleServiceProvider::class,
    Modules\Country\Providers\CountryModuleServiceProvider::class,
    Modules\Language\Providers\LanguageModuleServiceProvider::class,
    Modules\Provider\Providers\ProviderModuleServiceProvider::class,
    Modules\Source\Providers\SourceModuleServiceProvider::class,
    Modules\User\Providers\UserModuleServiceProvider::class,
];
