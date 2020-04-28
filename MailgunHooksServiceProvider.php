<?php

namespace CBDCRestigouche\MailgunHooks;

use App;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use League\CommonMark\Environment;

class MailgunHooksServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(__DIR__.'/config/mailgunhooks.php', 'mailgunhooks');
	}
	
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot(Router $router)
	{
		// Exports config file
		$this->publishes([
			__DIR__.'/config/mailgunhooks.php' => config_path('mailgunhooks.php'),
		]);
		
		// Register middleware
		$router->aliasMiddleware('mailgunhooks:webhook', MailgunHooksAuthorize::class);
		
		// Register webhook routes
		Route::prefix('webhooks')
			->middleware('mailgunhooks:webhook')
			->namespace('CBDCRestigouche\\MailgunHooks')
			->group(function(){ $this->loadRoutesFrom(__DIR__.'./routes.php'); });
		
		// Sets mailgun webhooks if we're not in local
		if (App::environment('production')) {
		   MailgunHooksController::setWebhooks();
		}
	}
}
