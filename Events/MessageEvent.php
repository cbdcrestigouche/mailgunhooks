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
	 * Data values that can be accessed by magic getters
	 */
	private $dataGetable = [
		'event',
		'timestamp',
		'id',
	];
    
    /**
     * Create a new event instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct($payload)
    {
        $this->signature = $payload->get('signature');
        $this->data = $payload->get('data');
	}
	
	/**
	 * A magic getter for event data
	 */
	public function __get($key)
	{
		if (!in_array($key, $this->dataGetable))
			return null;
		
		return $this->data[$key];
	}
}