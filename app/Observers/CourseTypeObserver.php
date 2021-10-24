<?php

namespace App\Observers;

use App\CustomClasses\SgcLogger;
use App\Models\CourseType;

class CourseTypeObserver
{
    /**
     * Handle the CourseType "created" event.
     *
     * @param  \App\Models\CourseType  $courseType
     * @return void
     */
    public function created(CourseType $courseType)
    {
        SgcLogger::writeLog(target: 'CourseType', action: 'created', model: $courseType);
    }

    /**
     * Handle the CourseType "updated" event.
     *
     * @param  \App\Models\CourseType  $courseType
     * @return void
     */
    public function updated(CourseType $courseType)
    {
        SgcLogger::writeLog(target: 'CourseType', action: 'updated', model: $courseType);
    }

    /**
     * Handle the CourseType "deleted" event.
     *
     * @param  \App\Models\CourseType  $courseType
     * @return void
     */
    public function deleted(CourseType $courseType)
    {
        SgcLogger::writeLog(target: 'CourseType', action: 'deleted', model: $courseType);
    }

    /**
     * Handle the CourseType "restored" event.
     *
     * @param  \App\Models\CourseType  $courseType
     * @return void
     */
    public function restored(CourseType $courseType)
    {
        //
    }

    /**
     * Handle the CourseType "force deleted" event.
     *
     * @param  \App\Models\CourseType  $courseType
     * @return void
     */
    public function forceDeleted(CourseType $courseType)
    {
        //
    }
}
