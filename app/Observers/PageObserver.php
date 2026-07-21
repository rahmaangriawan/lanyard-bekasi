<?php

namespace App\Observers;

use App\Models\Page;
class PageObserver
{
    private function clearCache(): void
    {
        // No page-level runtime cache currently depends on this model.
    }

    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(Page $page): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(Page $page): void
    {
        $this->clearCache();
    }
}
