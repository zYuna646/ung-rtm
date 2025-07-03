<?php

namespace App\Providers;

use App\Services\AmiService;
use App\Services\SurveiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Component;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(AmiService::class, function ($app) {
            return new AmiService();
        });

        $this->app->singleton(SurveiService::class, function ($app) {
            return new SurveiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void 
    {
        // Allow HTML content in specific Livewire component properties
        Component::macro('getRichTextEditorRules', function () {
            return [
                'rtmReport.tujuan' => [
                    function ($attribute, $value, $fail) {
                        // This is a bypass rule to allow HTML content
                    },
                ],
                'rtmReport.agenda_kegiatan' => [
                    function ($attribute, $value, $fail) {
                        // This is a bypass rule to allow HTML content
                    },
                ],
                'rtmReport.peserta' => [
                    function ($attribute, $value, $fail) {
                        // This is a bypass rule to allow HTML content
                    },
                ],
                'rtmReport.hasil' => [
                    function ($attribute, $value, $fail) {
                        // This is a bypass rule to allow HTML content
                    },
                ],
                'rtmReport.kesimpulan' => [
                    function ($attribute, $value, $fail) {
                        // This is a bypass rule to allow HTML content
                    },
                ],
                'rtmReport.penutup' => [
                    function ($attribute, $value, $fail) {
                        // This is a bypass rule to allow HTML content
                    },
                ],
            ];
        });
    }
}
