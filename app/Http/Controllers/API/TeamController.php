<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function create(CreateTeamRequest $request)
    {
        try {
            if($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            $team = Team::create([
                'name' => $request->name,
                'icon' => isset($path) ? $path : '',
                'company_id' => $request->company_id
            ]);

            if(!$team) {
                throw new Exception("Team not created");
            }

            return ResponseFormatter::success($team, 'Team created');
        } catch (Exception $error ) {
            return ResponseFormatter::error ($error->getMessage(), 500);
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            // Get Team
            $team = Team::find($id);
            // Check if Team exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // Upload logo
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Update Team
            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id
            ]);

            return ResponseFormatter::success($team, 'Team updated');

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamQuery = Team::withCount('employees');

        // Get single data
        if($id)
        {
            $team = $teamQuery->find($id);

            if ($team)
            {
                return ResponseFormatter::success($team, 'Team Found');
            }
            return ResponseFormatter::error('Team Not Found', 404);
        }
        // Get multiple data
        $teams = $teamQuery->where('company_id', $request->company_id);

        if ($name)
        {
            // powerhuman.com/api/team?name=...
            $teams->where('name', 'like', '%' . $name . '%');
        }
        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams Data Found'
        );
    }

    public function destroy($id)
    {
        try {
            // Get team
            $team = Team::find($id);

            // TODO: check if team owned by user
            // check if team exist
            if(!$team){
                throw new Exception('Team not found');
            }
            // Delete team
            $team->delete();

            return ResponseFormatter::success('Team deleted');


        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }

    }
}
