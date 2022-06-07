<?php

namespace App\Observers;

use App\CustomClasses\SgcLogger;
use App\Models\Employee;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function created(Employee $employee)
    {
        SgcLogger::writeLog(target: 'Employee', action: __FUNCTION__, model_json: $employee->toJson(JSON_UNESCAPED_UNICODE));
    }

    /**
     * Handle the Employee "updated" event.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function updating(Employee $employee)
    {
        SgcLogger::writeLog(target: 'Employee', action: __FUNCTION__, model_json: json_encode($employee->getOriginal(), JSON_UNESCAPED_UNICODE));
    }

    /**
     * Handle the Employee "updated" event.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function updated(Employee $employee)
    {
        SgcLogger::writeLog(target: 'Employee', action: __FUNCTION__, model_json: $employee->toJson(JSON_UNESCAPED_UNICODE));
    }

    /**
     * Handle the Employee "deleted" event.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function deleted(Employee $employee)
    {
        SgcLogger::writeLog(target: 'Employee', action: __FUNCTION__, model_json: $employee->toJson(JSON_UNESCAPED_UNICODE));
    }

    /**
     * Handle the Employee "restored" event.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function restored(Employee $employee)
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function forceDeleted(Employee $employee)
    {
        //
    }

    public function listed()
    {
        SgcLogger::writeLog(target: 'Employee', action: __FUNCTION__);
    }

    public function fetched(Employee $employee)
    {
        SgcLogger::writeLog(target: 'Employee', action: __FUNCTION__, model_json: $employee->toJson(JSON_UNESCAPED_UNICODE));
    }
}
