<?php

namespace CBDCRestigouche\MailgunHooks;

use Illuminate\Http\Request;
use CBDCRestigouche\MailgunHooks\Events\UnauthorizedEvent;
use Closure;

class AuthorizeEvents
{
	public function handle(Request $request, Closure $next)
	{
		$payload = $request->json();
		$signature = $payload->get('signature');
		
		if (!MailgunHooksFacade::verifySignature($signature))
		{
			if (config('mailgunhooks.unauthorizedEvent'))
			{
				$data = $payload->get('event-data');
				event(new UnauthorizedEvent($signature, $data));
			}
			
			abort(403);
		}
		
		return $next($request);
	}
}