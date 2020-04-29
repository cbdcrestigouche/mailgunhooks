<?php

namespace CBDCRestigouche\MailgunHooks;

use Exception;
use Illuminate\Support\Facades\Http;

class MailgunHooks
{
	/**
	 * Ensures that APIs are registered with mailgun.
	 */
	public function setWebhooks($host = null)
	{
		// Get existing webhooks
		$hooks = $this->getHooks($host);
		
		// Abort if this domain isn't registered
		if ($hooks === null) {
			throw new Exception('domain '.config('services.mailgun.domain').' is not registered with your Mailgun account.');
		}
		
		// Set desired webhooks based on config
		foreach (config('mailgunhooks.events') as $hookname => $should) {
			$url = 'https://'.config('services.mailgun.domain').'/webhooks/mgh/'.$hookname;
			$this->setHook($hooks, $hookname, $url, $should);
		}
	}
	
	/**
	 * Request the list of mailgun webhooks for a given domain name (this app's hostname by default).
	 * 
	 * @param string
	 * @return array
	 */
	public function getHooks($host = null)
	{
		$endpoint = $this->endpoint('/domains/<domain>/webhooks', $host);
		$response = $this->basicAuth()->get($endpoint);
		return $response->json()['webhooks'] ?? null;
	}
	
	/**
	 * Sets a webhook or unsets it, depending on the value of $force.
	 * 
	 * @param array the json array containing this domain's existing hooks
	 * @param string the name of the webhook to set
	 * @param string the full url to which the webhook should send events
	 * @param bool whether to force the event to be set or to be unset
	 */
	public function setHook($hooks, $name, $url, $force)
	{
		$hasHook = array_key_exists($name, $hooks);
		$hasUrl = $hasHook && in_array($url, $hooks[$name]['urls']);
		
		if ($force && !$hasUrl)
		{
			if ($hasHook) // update webhook url
			{
				$data = ['url' => $url];
				$this->basicAuth()->asForm()
					->put($this->endpoint('/domains/<domain>/webhooks/'.$name), $data);
			}
			else // create new webhook url
			{
				$data = ['id'  => $name, 'url' => $url];
				$this->basicAuth()->asForm()
					->post($this->endpoint('/domains/<domain>/webhooks'), $data);
			}
		}
		else if (!$force && $hasUrl)
		{
			$this->basicAuth()->delete($this->endpoint('/domains/<domain>/webhooks/'.$name));
		}
	}
	
	/**
	 * Returns a pending request which uses your Mailgun api key for basic auth.
	 * 
	 * @return \Illuminate\Http\Client\PendingRequest
	 */
	public function basicAuth()
	{
		return Http::withBasicAuth('api', config('services.mailgun.secret'));
	}
	
	/**
	 * Get the name of a mailgun api endpoint. If $host is not provided, uses your app's hostname.
	 * 
	 * @param string
	 * @param string
	 */
	public function endpoint($suffix, $host = null)
	{
		$host = $host ?? config('services.mailgun.domain');
		$suffix = '/v3'.str_replace('<domain>', $host, $suffix);
		return 'https://'.config('services.mailgun.endpoint').$suffix;
	}
	
	/**
	 * Verifies the signature of an event using your Mailgun api key.
	 * 
	 * @param array An array containing the "signature" part of a webhook event.
	 */
	protected static function verifySignature($sig)
	{
		$timestamp = $sig['timestamp'];
		$token     = $sig['token'];
		$signature = $sig['signature'];
		
		$hash = hash_hmac('sha256', $timestamp.$token, config('services.mailgun.secret'));
		return $signature === $hash;
	}
}