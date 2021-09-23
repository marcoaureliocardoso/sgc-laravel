<?php

namespace App\Services;

use App\Models\Role;
use App\CustomClasses\SgcLogger;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleService
{
    /**
     * Undocumented function
     *
     * @return LengthAwarePaginator
     */
    public function list(): LengthAwarePaginator
    {
        SgcLogger::writeLog(target: 'Role', action: 'index');

        $roles_query = new Role();
        $roles_query = $roles_query->AcceptRequest(Role::$accepted_filters)->filter();
        $roles_query = $roles_query->sortable(['name' => 'asc']);
        $roles = $roles_query->paginate(10);
        $roles->withQueryString();

        return $roles;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return Role
     */
    public function create(array $attributes): Role
    {
        $role = new Role;

        $role->name = $attributes['name'];
        $role->description = $attributes['description'];
        $role->grant_value = $attributes['grantValue'];
        $role->grant_type_id = $attributes['grantTypes'];

        SgcLogger::writeLog(target: $role);

        $role->save();

        return $role;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @param Role $role
     * @return Role
     */
    public function update(array $attributes, Role $role): Role
    {
        $role->name = $attributes['name'];
        $role->description = $attributes['description'];
        $role->grant_value = $attributes['grantValue'];
        $role->grant_type_id = $attributes['grantTypes'];

        SgcLogger::writeLog(target: $role, action: 'update');

        $role->save();

        return $role;
    }

    /**
     * Undocumented function
     *
     * @param Role $role
     * @return void
     */
    public function delete(Role $role)
    {
        SgcLogger::writeLog(target: $role, action: 'destroy');

        $role->delete();
    }
}