<?php

namespace App\Services;

use App\Models\Role;
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
        (new Role)->logListed();

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
        $role = Role::create($attributes);

        return $role;
    }

    /**
     * Undocumented function
     *
     * @param Role $role
     * @return Role
     */
    public function read(Role $role): Role
    {
        $role->logFetched($role);

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
        $role->update($attributes);

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
        $role->delete();
    }
}
