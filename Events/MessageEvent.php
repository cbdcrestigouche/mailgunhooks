<?php

namespace CBDCRestigouche\MailgunHooks\Events;

/**
 * @property string event The name of the Mailgun event
 * @property string timestamp The timestamp of the Mailgun event
 * @property string id The id of the Mailgun event
 */
class MessageEvent
{
	/**
     * The event signature.
     * 
     * @var array
     */
    public $signature;
    
    /**
     * The event data.
     *
     * @var array
     */
	public $data;
    
    /**
     * Create a new event instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct($payload)
    {
        $this->signature = $payload->get('signature');
        $this->data = $payload->get('event-data');
	}
	
	/**
	 * A magic getter for event data
	 */
	public function __get($key)
	{
		return $this->data[$key] ?? null;
	}
}