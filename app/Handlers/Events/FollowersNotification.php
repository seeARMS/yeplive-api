<?php namespace App\Handlers\Events;

use App\Events\YepCreated;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class FollowersNotification {

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
		\Log::info("construct yo");
	}

	/**
	 * Handle the event.
	 *
	 * @param  YepCreated  $event
	 * @return void
	 */
	public function handle(YepCreated $event)
	{
		\Log::info("event");
	}

}
