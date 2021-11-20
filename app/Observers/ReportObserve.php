<?php

namespace App\Observers;

use App\Models\Report;

class ReportObserve
{
    /**
     * Handle the Report "created" event.
     *
     * @param  \App\Models\Report  $report
     * @return void
     */
    public function created(Report $report)
    {
        //
    }

    /**
     * Handle the Report "updated" event.
     *
     * @param  \App\Models\Report  $report
     * @return void
     */
    public function updated(Report $report)
    {
        //
    }

    /**
     * Handle the Report "deleted" event.
     *
     * @param  \App\Models\Report  $report
     * @return void
     */
    public function deleted(Report $report)
    {
        $report->comments()->delete();
    }

    /**
     * Handle the Report "restored" event.
     *
     * @param  \App\Models\Report  $report
     * @return void
     */
    public function restored(Report $report)
    {
        //
    }

    /**
     * Handle the Report "force deleted" event.
     *
     * @param  \App\Models\Report  $report
     * @return void
     */
    public function forceDeleted(Report $report)
    {
        //
    }
}
