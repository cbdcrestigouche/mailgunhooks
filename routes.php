<?php

foreach (config('mailgunhooks.events') as $hookname => $should) if ($should) {
    Route::post('mgh/'.$hookname, 'MailgunHooksController@'.$hookname)
        ->name('webhooks.mailgunhooks.'.$hookname);
}