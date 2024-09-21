<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserActivity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Authenticated $event
     * @return void
     */
    public function handle(Authenticated $event)
    {
        $user = $event->user;

        Log::info("User logged in: " . $user);

        $user->update([
            'last_active_at' => now(),
            'is_active' => true,
        ]);
    }
}
