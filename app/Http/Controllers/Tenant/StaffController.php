<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $tenant = tenant('id');
        $staff  = TenantUser::where('role', '!=', 'owner')->orderBy('name')->get();
        return view('tenant.staff.index', compact('staff', 'tenant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:waiter,kitchen',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Ya existe un usuario con ese correo.',
        ]);

        TenantUser::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Usuario \"{$request->name}\" creado.");
    }

    public function destroy(TenantUser $staff)
    {
        if ($staff->isOwner()) {
            return back()->with('error', 'No se puede eliminar al dueño.');
        }

        $staff->delete();
        return back()->with('success', 'Usuario eliminado.');
    }
}
