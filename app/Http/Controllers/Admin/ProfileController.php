<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Imports\UserImport;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProfileController extends Controller
{
    protected $userRepository;
    protected $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function index(): View
    {
        return view('profile.index');
    }

    public function getProfiles()
    {
        $profiles = $this->userRepository->all();
        return DataTables::of($profiles)
            ->addColumn('id', function ($profile) {
                return $profile->id;
            })
            ->addColumn('name', function ($profile) {
                return $profile->name;
            })
            ->addColumn('email', function ($profile) {
                return $profile->email;
            })
            ->addColumn('roles', function ($profile) {
                $roles = '<ul>';
                foreach ($profile->roles as $role) {
                    $roles .= '<li>' . $role->name . '</li>';
                }
                $roles .= '</ul>';
                return $roles;
            })
            ->addColumn('formatted_created_at', function ($profile) {
                return $profile->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($profile) {
                return $profile->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.profile-action')
            ->rawColumns(['action', 'roles'])
            ->addIndexColumn()
            ->make(true);
    }

    public function tableProfile($id)
    {
        $profiles = $this->userRepository->findGet($id);
        return DataTables::of($profiles)
            ->addColumn('id', function ($profile) {
                return $profile->id;
            })
            ->addColumn('name', function ($profile) {
                return $profile->name;
            })
            ->addColumn('email', function ($profile) {
                return $profile->email;
            })
            ->addColumn('roles', function ($profile) {
                $roles = '<ul>';
                foreach ($profile->roles as $role) {
                    $roles .= '<li>' . $role->name . '</li>';
                }
                $roles .= '</ul>';
                return $roles;
            })
            ->addColumn('formatted_created_at', function ($profile) {
                return $profile->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($profile) {
                return $profile->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.profile-action')
            ->rawColumns(['action', 'roles'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create(): View
    {
        return view('profile.create');
    }

    public function show($id): JsonResponse
    {
        $data = $this->userRepository->find($id);
        return response()->json($data);
    }

    public function store(UserRequest $userRequest)
    {
        $input = $userRequest->validated();
        try {
            $role = Role::find($input['role_id']);
            $user = $this->userRepository->create($input);
            $user->assignRole($role->name);
            return redirect()->route('profile.index')->with('success', 'User Created Successfully!');
        } catch (\Exception $e) {
            return redirect()->route('profile.index')->with('error', 'Creating User Failed: ' . $e->getMessage());
        }
    }

    public function edit(string $id): View
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return redirect()->route('profile.index')->with('error', 'User not found.');
        }

        return view('profile.edit', compact('user'));
    }

    public function update(UserRequest $userRequest, string $id)
    {
        $input = $userRequest->validated();

        try {
            $user = $this->userRepository->update($id, $input);

            if (!$user) {
                return redirect()->route('profile.index')->with('error', 'User not found.');
            }

            return redirect()->route('profile.index')->with('success', 'User Updated Successfully!');
        } catch (\Exception $e) {
            return redirect()->route('profile.index')->with('error', 'Updating User Failed: ' . $e->getMessage());
        }
    }

    public function destroy($profile)
    {
        $deleted = $this->userRepository->delete($profile);

        if (!$deleted) {
            return redirect()->route('profile.index')->with('error', 'User not found.');
        }

        return redirect()->route('profile.index')->with('success', 'User Deleted Successfully!');
    }

    public function exportProfiles()
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }

    public function importProfiles()
    {
        try {
            DB::beginTransaction();

            Excel::import(new UserImport, request()->file('file'));


            return redirect()->back()->with('success', 'Import successful');
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
