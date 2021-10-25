<?php

namespace App\Observers;

use App\CustomClasses\SgcLogger;
use App\Models\Course;

class CourseObserver
{
    /**
     * Handle the Course "created" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function created(Course $course)
    {
        SgcLogger::writeLog(target: 'Course', action: __FUNCTION__, model: $course);
    }

    /**
     * Handle the Course "updated" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function updating(Course $course)
    {
        SgcLogger::writeLog(target: 'Course', action: __FUNCTION__, model: $course);
    }

    /**
     * Handle the Course "updated" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function updated(Course $course)
    {
        SgcLogger::writeLog(target: 'Course', action: __FUNCTION__, model: $course);
    }

    /**
     * Handle the Course "deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function deleted(Course $course)
    {
        SgcLogger::writeLog(target: 'Course', action: __FUNCTION__, model: $course);
    }

    /**
     * Handle the Course "restored" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function restored(Course $course)
    {
        //
    }

    /**
     * Handle the Course "force deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function forceDeleted(Course $course)
    {
        //
    }

    public function listed()
    {
        SgcLogger::writeLog(target: 'Course', action: __FUNCTION__);
    }

    public function viewed(Course $approved)
    {
        SgcLogger::writeLog(target: 'Course', action: __FUNCTION__, model: $approved);
    }
}
