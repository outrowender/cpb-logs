<?php

namespace wendrpatrck\cpblogs;

use wendrpatrck\cpblogs\Reporter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__ . '/config/cpblogs.php' => config_path('cpblogs.php')], 'config');
        $this->mergeConfigFrom(__DIR__ . '/config/cpblogs.php', 'cpblogs');
    }

    public function register()
    {
        try {
            //ouve todos os logs lançados no sistema
            Log::listen(function ($item) {
                $exception = $item->context['exception'] ?? null;

                if (isset($exception)) {
                    Reporter::reportException($exception);
                } else {
                    \error_log('cpblogs: Nenhum dado para reportar no log');
                }
            });
        } catch (\Exception $ex) {
            \error_log('cpblogs: Erro detectado mas não é possível reportar:' . $ex->getMessage());
        }
    }
}
