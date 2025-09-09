<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ApiVersionServiceProvider::class,
    Laravel\Passport\PassportServiceProvider::class,
    modules\Article\Providers\ArticleModuleServiceProvider::class,
    modules\Author\Providers\AuthorModuleServiceProvider::class,
    modules\Category\Providers\CategoryModuleServiceProvider::class,
    modules\Country\Providers\CountryModuleServiceProvider::class,
    modules\Language\Providers\LanguageModuleServiceProvider::class,
    modules\Provider\Providers\ProviderModuleServiceProvider::class,
    modules\Source\Providers\SourceModuleServiceProvider::class,
    modules\User\Providers\UserModuleServiceProvider::class,
];
