<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminManagementController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.manage', compact('users'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'verification_password' => 'required'
        ]);

        // Verifikasi password admin yang membuat
        if (!Hash::check($request->verification_password, Auth::user()->password)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Password verifikasi salah']);
            }
            return back()->withInput()->with('error', 'Password verifikasi salah');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Admin berhasil ditambahkan']);
        }
        return redirect()->route('admin.manage')->with('success', 'Admin berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('admin.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,staff',
            'verification_password' => 'required',
            'new_password' => 'nullable|min:8|confirmed'
        ]);

        // Verifikasi password admin yang mengedit
        if (!Hash::check($request->verification_password, Auth::user()->password)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Password verifikasi salah']);
            }
            return back()->with('error', 'Password verifikasi salah');
        }

        // Update data
        $updateData = ['role' => $request->role];

        // Jika ada password baru
        if ($request->filled('new_password')) {
            $updateData['password'] = Hash::make($request->new_password);
        }

        $user->update($updateData);

        if ($request->ajax()) {
            return response()->json(['success' => 'Data user berhasil diperbarui']);
        }
        return redirect()->route('admin.manage')->with('success', 'Data user berhasil diperbarui');
    }

    public function destroy(Request $request, User $user)
    {
        $request->validate([
            'verification_password' => 'required'
        ]);

        // Verifikasi password admin yang menghapus
        if (!Hash::check($request->verification_password, Auth::user()->password)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Password verifikasi salah']);
            }
            return back()->with('error', 'Password verifikasi salah');
        }

        // Cek apakah user menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Anda tidak dapat menghapus akun Anda sendiri']);
            }
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        $user->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'User berhasil dihapus']);
        }
        return redirect()->route('admin.manage')->with('success', 'User berhasil dihapus');
    }
} 