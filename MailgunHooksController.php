<?php

namespace CBDCRestigouche\MailgunHooks;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;
use CBDCRestigouche\MailgunHooks\Events\MessageClicked;
use CBDCRestigouche\MailgunHooks\Events\MessageComplained;
use CBDCRestigouche\MailgunHooks\Events\MessageDelivered;
use CBDCRestigouche\MailgunHooks\Events\MessageOpened;
use CBDCRestigouche\MailgunHooks\Events\MessagePermanentFail;
use CBDCRestigouche\MailgunHooks\Events\MessageTemporaryFail;
use CBDCRestigouche\MailgunHooks\Events\MessageUnsubscribe;

class MailgunHooksController extends Controller
{
    /**
     * Ensures that APIs are registered with mailgun.
     */
    public static function setWebhooks()
    {
        // Get existing webhooks
        $hooks = self::getHooks();
        
        // Set desired webhooks based on config
        foreach (config('mailgunhooks.events') as $hookname => $should) {
            $url = route('webhooks.mailgunhooks.'.$hookname);
            self::setHook($hooks, $hookname, $url, $should);
        }
    }
    
    /**
     * Returns a pending request which uses your Mailgun api key for basic auth.
     * 
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public static function basicAuth()
    {
        return Http::withBasicAuth('api', config('services.mailgun.secret'));
    }
    
    /**
     * Get the name of an api endpoint. If $host is not provided, uses your app's hostname.
     * 
     * @param string
     * @param string
     */
    public static function endpoint($suffix, $host = null)
    {
        $host = $host ?? Request::getHost();
        $suffix = str_replace('<domain>', $host, $suffix);
        return config('services.mailgun.endpoint').$suffix;
    }
    
    /**
     * Request the list of webhooks for a given domain name.
     * 
     * @param string
     * @return array
     */
    public static function getHooks($host = null)
    {
        $endpoint = self::endpoint('/domains/<domain>/webhooks', $host);
        $response = self::basicAuth()->get($endpoint);
        return $response->json();
    }
    
    /**
     * Sets a webhook or unsets it, depending on the value of $force.
     * 
     * @param array the json array containing this domain's existing hooks
     * @param string the name of the webhook to set
     * @param string the full url to which the webhook should send events
     * @param bool whether to force the event to be set or to be unset
     */
    public static function setHook($hooks, $name, $url, $force)
    {
        $hasHook = array_key_exists($name, $hooks);
        $hasUrl = $hasHook && in_array($url, $hooks[$name]['urls']);
        
        if ($force && !$hasUrl)
        {
            if ($hasHook) // update webhook url
            {
                $data = ['url' => $url];
                self::basicAuth()->asForm()
                    ->put(self::endpoint('/domains/<domain>/webhooks/'.$name), $data);
            }
            else // create new webhook url
            {
                $data = ['id'  => $name, 'url' => $url];
                self::basicAuth()->asForm()
                    ->post(self::endpoint('/domains/<domain>/webhooks'), $data);
            }
        }
        else if (!$force && $hasHook)
        {
            self::basicAuth()->delete(self::endpoint('/domains/<domain>/webhooks/'.$name));
        }
    }
    
    /**
     * "Message clicked" event route.
     */
    public function clicked(Request $request)
    {
        event(new MessageClicked($request->json()));
    }
    
    /**
     * "Message complained about" event route.
     */
    public function complained(Request $request)
    {
        event(new MessageComplained($request->json()));
    }
    
    /**
     * "Message delivered" event route.
     */
    public function delivered(Request $request)
    {
        event(new MessageDelivered($request->json()));
    }
    
    /**
     * "Message opened" event route.
     */
    public function opened(Request $request)
    {
        event(new MessageOpened($request->json()));
    }
    
    /**
     * "Message permanent fail" event route.
     */
    public function permanent_fail(Request $request)
    {
        event(new MessagePermanentFail($request->json()));
    }
    
    /**
     * "Message temporary fail" event route.
     */
    public function temporary_fail(Request $request)
    {
        event(new MessageTemporaryFail($request->json()));
    }
    
    /**
     * "Message unsubscribed" event route.
     */
    public function unsubscribed(Request $request)
    {
        event(new MessageUnsubscribe($request->json()));
    }
}
