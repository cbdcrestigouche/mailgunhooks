<?php

namespace CBDCRestigouche\MailgunHooks;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Route;

class ServiceProvider extends BaseServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(__DIR__.'/config/mailgunhooks.php', 'mailgunhooks');
		
		$this->app->bind('mailgunhooks', function() {
			return new MailgunHooks();
		});
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
		$router->aliasMiddleware('mgh_authevents', AuthorizeEvents::class);
		
		// Abort if mailgun isn't used.
		if (!config('services.mailgun') || !config('services.mailgun.domain') || !config('services.mailgun.secret') || !config('services.mailgun.endpoint'))
			return;
		
		// Register webhook routes
		Route::prefix('webhooks')
			->middleware('mgh_authevents')
			->namespace('CBDCRestigouche\\MailgunHooks')
			->group(function(){ $this->loadRoutesFrom(__DIR__.'/routes.php'); });
			
		// Sets mailgun webhooks only if we're in production
		if (app()->environment('production') && !app()->isDownForMaintenance()) {
			MailgunHooksFacade::setWebhooks();
		}
	}
}
