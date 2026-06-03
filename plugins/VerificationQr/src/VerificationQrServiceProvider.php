<?php

namespace Plugins\VerificationQr;

use Illuminate\Support\ServiceProvider;

class VerificationQrServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'VerificationQr');
    }
}
