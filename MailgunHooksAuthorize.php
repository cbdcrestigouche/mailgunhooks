<?php

namespace CBDCRestigouche\MailgunHooks;

use CBDCRestigouche\MailgunHooks\Events\UnauthorizedEvent;
use Closure;
use Request;

class MailgunHooksAuthorize
{
	/**
	 * Verifies the signature of an event using your Mailgun api key.
	 * 
	 * @param string The event's timestamp
	 * @param string The event's token
	 * @param string The event's signature
	 * @return bool Whether or not the signature is verified using your Mailgun api key.
	 */
	protected static function verifySignature($sig)
	{
		$timestamp = $sig->get('timestamp');
		$token = $sig->get('token');
		$signature = $sig->get('signature');
		
		$hash = hash_hmac('sha256', $timestamp.$token, config('services.mailgun.secret'));
		return $signature === $hash;
	}
	
	public function handle(Request $request, Closure $next)
	{
		$payload = $request->json();
		$signature = $payload->get('signature');
		
		if (!self::verifySignature($signature))
		{
			if (config('mailgunhooks.unauthorizedEvent'))
			{
				$data = $payload->get('data');
				event(new UnauthorizedEvent($signature, $data));
			}
			
			abort(403);
		}
		
		return $next($request);
	}
}