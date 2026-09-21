<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withTrashed()->latest()
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('nip', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->role, function ($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->paginate($request->get('per_page', 10))
            ->appends($request->all());

        return view('system.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|digits:8|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:super_admin,manager,admin_gudang,kasir,purchasing',
        ]);

        $user = User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        AuditLog::record('USER_CREATED', 'User', $user->id, null, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nip' => ['required', 'digits:8', Rule::unique('users')->ignore($user->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:super_admin,manager,admin_gudang,kasir,purchasing'],
        ]);

        $oldValues = [
            'nip' => $user->nip,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $user->nip = $request->nip;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8',
            ]);
            $user->password = Hash::make($request->password);
        }

        $dirty = $user->getDirty();
        $user->save();

        if (count($dirty) > 0) {
            AuditLog::record('USER_UPDATED', 'User', $user->id, $oldValues, array_diff_key($dirty, ['password' => '']));

            $attributeNames = [
                'nip' => 'NIP',
                'name' => 'Nama',
                'email' => 'Email',
                'role' => 'Role Akses',
                'password' => 'Kata Sandi',
            ];

            $changedText = collect(array_keys($dirty))
                ->map(fn ($attr) => $attributeNames[$attr] ?? $attr)
                ->implode(', ');

            $message = "Data pengguna {$user->name} berhasil diperbarui ({$changedText})";
        } else {
            $message = "Tidak ada perubahan data pada pengguna {$user->name}";
        }

        return redirect()->back()->with('success', $message);
    }

    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        if ($user->trashed()) {
            AuditLog::record('USER_FORCE_DELETED', 'User', $user->id, ['name' => $user->name, 'email' => $user->email]);
            $user->forceDelete();

            return redirect()->back()->with('success', 'User berhasil dihapus permanen');
        } else {
            AuditLog::record('USER_SOFT_DELETED', 'User', $user->id, ['name' => $user->name, 'email' => $user->email]);
            $user->delete();

            return redirect()->back()->with('success', 'User berhasil dihapus sementara (Soft Delete)');
        }
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        AuditLog::record('USER_RESTORED', 'User', $user->id, null, ['name' => $user->name, 'email' => $user->email]);

        return redirect()->back()->with('success', 'User berhasil dipulihkan');
    }

    public function toggleActive($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah status akun Anda sendiri');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        AuditLog::record('USER_STATUS_TOGGLED', 'User', $user->id, null, [
            'name' => $user->name,
            'is_active' => $user->is_active,
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan (Block)';

        return redirect()->back()->with('success', "User berhasil $status");
    }
}
