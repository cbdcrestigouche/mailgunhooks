<?php

foreach (config('mailgunhooks.events') as $hookname => $should) if ($should) {
	Route::post('mgh/'.$hookname, 'EventsController@'.$hookname)
		->name('webhooks.mgh.'.$hookname);
}