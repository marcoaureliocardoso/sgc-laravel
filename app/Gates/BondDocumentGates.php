<?php

namespace App\Gates;

use Illuminate\Support\Facades\Gate;

class BondDocumentGates
{
    public static function define()
    {
        Gate::define('bondDocument-rights', function ($user) {
            //who can do it (global).
            if (Gate::forUser($user)->any(['is-Adm-global', 'is-Dir-global', 'is-Ass-global', 'is-Sec-global', 'is-Ldi-global'])) return true;

            //coords of any course
            if (Gate::forUser($user)->any(['isGra'])) return true;

            //no permission
            return false;
        });
    }
}
