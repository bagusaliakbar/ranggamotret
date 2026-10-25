<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controller Manajemen Pengguna & Hak Akses (ICONIX Robustness: UserController)
 * 
 * Merepresentasikan Control Object 'UserController' pada Robustness Diagram.
 * Terhubung dengan boundary 'profile', 'registrasi', 'login' dan entity 'User'.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $status = $request->query('status');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['owner', 'pelanggan', 'admin', 'customer', 'peserta'])) {
            if (in_array($role, ['owner', 'admin'])) {
                $query->whereIn('role', ['owner', 'admin']);
            } else {
                $query->whereIn('role', ['pelanggan', 'customer', 'peserta']);
            }
        }

        if ($status && in_array($status, ['active', 'inactive'])) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // Statistics
        $totalUsers = User::count();
        $totalOwners = User::whereIn('role', ['owner', 'admin'])->count();
        $totalPelanggan = User::whereIn('role', ['pelanggan', 'customer', 'peserta'])->count();
        $totalAdmins = $totalOwners;
        $totalCustomers = $totalPelanggan;

        return view('users.admin', compact(
            'users',
            'totalUsers',
            'totalOwners',
            'totalPelanggan',
            'totalAdmins',
            'totalCustomers',
            'search',
            'role',
            'status'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:owner,pelanggan,admin,customer,peserta',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ]);

        $finalRole = in_array($validated['role'], ['owner', 'admin']) ? 'owner' : 'pelanggan';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $finalRole,
            'status' => $validated['status'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if ($finalRole === 'pelanggan') {
            Customer::firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $validated['phone'] ?? null, 'address' => $validated['address'] ?? null]
            );
        }

        return redirect()->route('admin.users.index')->with('success', "Pengguna '{$user->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:owner,pelanggan,admin,customer,peserta',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ]);

        $finalRole = in_array($validated['role'], ['owner', 'admin']) ? 'owner' : 'pelanggan';

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $finalRole;
        $user->status = $validated['status'];
        $user->phone = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($finalRole === 'pelanggan') {
            Customer::updateOrCreate(
                ['user_id' => $user->id],
                ['phone' => $validated['phone'] ?? null, 'address' => $validated['address'] ?? null]
            );
        }

        return redirect()->route('admin.users.index')->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Pengguna '{$userName}' berhasil dihapus dari sistem.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        $statusText = ($user->status === 'active') ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.users.index')->with('success', "Status akun '{$user->name}' berhasil {$statusText}.");
    }
}
