<?php

namespace CBDCRestigouche\MailgunHooks;

use Illuminate\Support\Facades\Facade;

class MailgunHooksFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mailgunhooks';
    }
}