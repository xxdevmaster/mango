<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->composerHeader();

        Blade::directive('asaha', function($expression) {
            return "<?php echo 'nmnmnmnmn'.{$expression}; ?>";
        });


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function composerHeader()
    {
        view()->composer('partials.header', 'App\Http\Composers\ViewComposer');
    }
}
