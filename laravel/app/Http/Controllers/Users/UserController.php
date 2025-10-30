<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Membuat query builder dengan eager load relasi yang diperlukan
                $query = User::with('roles', 'profile');

                return DataTables::of($query)
                    // Tambahkan kolom "name" yang dihitung dari relasi profile
                    ->addColumn('name', function ($user) {
                        return trim(optional($user->profile)->first_name . ' ' . optional($user->profile)->last_name);
                    })
                    ->editColumn('email', function ($user) {
                        return $user->email;
                    })
                    ->editColumn('username', function ($user) {
                        return $user->username;
                    })
                    ->editColumn('status', function ($user) {
                        $status = $user->status;
                        $statusColors = [
                            'ACTIVE' => 'success',
                            'PENDING' => 'warning',
                            'BLOCK' => 'danger',
                            'INACTIVE' => 'secondary',
                            'SUSPENDED' => 'info',
                            'DELETED' => 'dark',
                            'BANNED' => 'danger',
                            'EXPIRED' => 'secondary',
                        ];
                        $labels = [
                            'ACTIVE' => 'Active',
                            'PENDING' => 'Pending',
                            'BLOCK' => 'Blocked',
                            'INACTIVE' => 'Inactive',
                            'SUSPENDED' => 'Suspended',
                            'DELETED' => 'Deleted',
                            'BANNED' => 'Banned',
                            'EXPIRED' => 'Verification Expired',
                        ];
                        $color = $statusColors[$status] ?? 'secondary';
                        $label = $labels[$status] ?? 'Verification Expired';
                        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
                    })
                    // Tambahkan kolom role_name dengan badge untuk tiap peran
                    ->addColumn('role_name', function ($user) {
                        $roleNames = $user->roles->pluck('name')->implode(', ');
                        $roles = explode(', ', $roleNames);
                        $badges = '';
                        $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
                        foreach ($roles as $role) {
                            $index = crc32($role) % count($colors);
                            $color = $colors[$index];
                            $badges .= '<span class="badge bg-' . $color . '">' . $role . '</span> ';
                        }
                        return $badges;
                    })
                    // Tambahkan kolom role_id jika diperlukan
                    ->addColumn('role_id', function ($user) {
                        return $user->roles->pluck('id')->first();
                    })
                    ->addColumn('actions', function ($user) {
                        // Nama diambil dari relasi profile (gunakan trim agar tidak ada spasi ekstra)
                        $name = trim(optional($user->profile)->first_name . ' ' . optional($user->profile)->last_name);
                        $btn = '<button class="btn btn-warning btn-sm btn-edit" data-id="' . $user->id . '" ' .
                            'data-name="' . $name . '" ' .
                            'data-email="' . $user->email . '" ' .
                            'data-role-id="' . $user->roles->pluck('id')->first() . '" ' .
                            'data-username="' . $user->username . '" ' .
                            'data-status="' . $user->status . '">Edit</button> ';
                        $btn .= '<button class="btn btn-danger btn-sm btn-delete" data-id="' . $user->id . '">Delete</button>';
                        return $btn;
                    })
                    ->rawColumns(['actions', 'role_name', 'status'])
                    ->make(true);
            }

            // Jika bukan request AJAX, ambil data untuk tampilan
            $roles = Role::all();
            $users = User::with('roles', 'profile')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => trim(optional($item->profile)->first_name . ' ' . optional($item->profile)->last_name),
                    'username' => $item->username,
                    'status' => $item->status,
                    'email' => $item->email,
                    'role_name' => $item->roles->pluck('name')->implode(', '),
                ];
            });

            return view('dashboard.users.index', compact('roles', 'users'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'modalUsername' => 'required|min:3|unique:users,username',
                'modalStatus' => 'required',
                'modalEmail' => 'required|email|unique:users,email',
                'modalPassword' => 'required|min:6',
                'modalRole' => 'required'
            ]);

            $user = User::create([
                'username' => $validated['modalUsername'],
                'status' => $validated['modalStatus'],
                'email' => $validated['modalEmail'],
                'password' => bcrypt($validated['modalPassword'])
            ]);

            $role = Role::findOrFail($validated['modalRole']);
            $user->assignRole($role->name);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'editEmail' => 'required|email|unique:users,email,' . $user->id,
                'editUsername' => 'required|min:3|unique:users,username,' . $user->id,
                'editStatus' => 'required',
                'editPassword' => 'nullable|min:6',
                'editRole' => 'required'
            ]);


            $user->update([
                'username' => $validated['editUsername'],
                'status' => $validated['editStatus'],
                'email' => $validated['editEmail']
            ]);

            if ($request->filled('editPassword')) {
                $user->update([
                    'password' => bcrypt($validated['editPassword'])
                ]);
            }

            $role = Role::findOrFail($validated['editRole']);
            $user->syncRoles([$role->name]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->syncRoles([]);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
