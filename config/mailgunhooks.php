<?php

return [
	/*
    |--------------------------------------------------------------------------
    | Registered Event
    |--------------------------------------------------------------------------
    |
	| Here we list the event names that should be emitted
    |
	*/
	
	'events' => [
		'clicked'        => false,
		'complained'     => false,
		'delivered'      => true,
		'opened'         => false,
		'permanent_fail' => true,
		'temporary_fail' => true,
		'unsubscribed'   => false,
	],
	
	/*
    |--------------------------------------------------------------------------
    | Emit an event for unauthorized Mailgun events
    |--------------------------------------------------------------------------
    |
	| If true, UnauthorizedEvent will be emitted when an
	| event from an unauthorized source is received.
    |
	*/
	
	'unauthorizedEvent' => true,
];