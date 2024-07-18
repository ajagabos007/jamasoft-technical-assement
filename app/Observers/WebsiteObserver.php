<?php

namespace App\Observers;

use App\Models\Website;

class WebsiteObserver
{
    /**
     * Handle the Website "created" event.
     */
    public function created(Website $website): void
    {
        //
    }

    /**
     * Handle the Website "updated" event.
     */
    public function updated(Website $website): void
    {
        //
    }

    /**
     * Handle the Website "deleted" event.
     */
    public function deleted(Website $website): void
    {
        $website->categories()->detach();
        $website->votes()->delete();
    }

    /**
     * Handle the Website "restored" event.
     */
    public function restored(Website $website): void
    {
        //
    }

    /**
     * Handle the Website "force deleted" event.
     */
    public function forceDeleted(Website $website): void
    {
        //
    }
}
