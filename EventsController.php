<?php

namespace CBDCRestigouche\MailgunHooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use CBDCRestigouche\MailgunHooks\Events\MessageClicked;
use CBDCRestigouche\MailgunHooks\Events\MessageComplained;
use CBDCRestigouche\MailgunHooks\Events\MessageDelivered;
use CBDCRestigouche\MailgunHooks\Events\MessageOpened;
use CBDCRestigouche\MailgunHooks\Events\MessagePermanentFail;
use CBDCRestigouche\MailgunHooks\Events\MessageTemporaryFail;
use CBDCRestigouche\MailgunHooks\Events\MessageUnsubscribe;

class EventsController extends Controller
{
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
