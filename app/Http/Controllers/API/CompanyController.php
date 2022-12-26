<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['users'])->whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        });

        // Get single data
        if($id)
        {
            $company = $companyQuery->find($id);

            if ($company)
            {
                return ResponseFormatter::success($company, 'Company Found');
            }
            return ResponseFormatter::error('Company Not Found', 404);
        }
        // Get multiple data
        $companies = $companyQuery;

        if ($name)
        {
            // powerhuman.com/api/company?name=...
            $companies->where('name', 'like', '%' . $name . '%');
        }
        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies Data Found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            if($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            $company = Company::create([
                'name' => $request->name,
                'logo' => $path
            ]);

            if(!$company) {
                throw new Exception("Company not created");
            }

            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            $company->load('users');

            return ResponseFormatter::success($company, 'Company created');
        } catch (Exception $error ) {
            return ResponseFormatter::error ($error->getMessage(), 500);
        }
    }

    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            // Get Company
            $company = Company::find($id);
            // Check if company exists
            if (!$company) {
                throw new Exception('Company not found');
            }

            // Upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // Update Company
            $company->update([
                'name' => $request->name,
                'logo' => $path
            ]);

            return ResponseFormatter::success($company, 'Company updated');

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
