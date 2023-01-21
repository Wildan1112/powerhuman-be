<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function create(CreateRoleRequest $request)
    {
        try {
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);

            if(!$role) {
                throw new Exception("Role not created");
            }

            return ResponseFormatter::success($role, 'Role created');
        } catch (Exception $error ) {
            return ResponseFormatter::error ($error->getMessage(), 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            // Get Role
            $role = Role::find($id);
            // Check if Role exists
            if (!$role) {
                throw new Exception('Role not found');
            }

            // Update Role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);

            return ResponseFormatter::success($role, 'Role updated');

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);

        // Get Multiple Data
        $roleQuery = Role::withCount('responsibilities');

        // Get single data
        if($id)
        {
            $role = $roleQuery->with('responsibilities')->find($id);

            if ($role)
            {
                return ResponseFormatter::success($role, 'Role Found');
            }
            return ResponseFormatter::error('Role Not Found', 404);
        }
        // Get multiple data
        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name)
        {
            // powerhuman.com/api/role?name=...
            $roles->where('name', 'like', '%' . $name . '%');
        }
        if ($with_responsibilities)
        {
            $roles->with('responsibilities');
        }
        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles Data Found'
        );
    }

    public function destroy($id)
    {
        try {
            // Get role
            $role = Role::find($id);

            // TODO: check if role owned by user
            // check if role exist
            if(!$role){
                throw new Exception('Role not found');
            }
            // Delete role
            $role->delete();

            return ResponseFormatter::success('Role deleted');


        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }
}
