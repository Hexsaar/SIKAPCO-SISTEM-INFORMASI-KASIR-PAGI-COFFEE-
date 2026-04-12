<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index()
    {
        $employees = Employee::with('user')->paginate(10);
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $users = User::whereDoesntHave('employee')->get();
        return view('employees.create', compact('users'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string|max:100',
            'user_id' => 'nullable|exists:users,id',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $users = User::whereDoesntHave('employee')->orWhere('id', $employee->user_id)->get();
        return view('employees.edit', compact('employee', 'users'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'position' => 'required|string|max:100',
            'user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    /**
     * Toggle employee active status.
     */
    public function toggle(Employee $employee)
    {
        $employee->update([
            'is_active' => !$employee->is_active
        ]);

        $status = $employee->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('employees.index')
            ->with('success', "Karyawan berhasil {$status}.");
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }

    /**
     * Get employees as JSON for sidebar panel
     */
    public function apiList()
    {
        $employees = Employee::select('id', 'name', 'position', 'is_active')
            ->with('user:id,profile_photo_path')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'is_active' => $employee->is_active,
                    'profile_photo_url' => $employee->user?->profile_photo_url ?? null,
                ];
            });

        return response()->json(['employees' => $employees]);
    }

    /**
     * Get single employee as JSON
     */
    public function apiShow(Employee $employee)
    {
        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'position' => $employee->position,
                'is_active' => $employee->is_active,
                'profile_photo_url' => $employee->user?->profile_photo_url ?? null,
            ]
        ]);
    }

    /**
     * Create employee via API
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string|max:100',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'is_active' => true
        ]);

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'position' => $employee->position,
                'is_active' => $employee->is_active,
            ]
        ], 201);
    }

    /**
     * Toggle employee active status via API
     */
    public function apiToggle(Employee $employee)
    {
        $employee->update([
            'is_active' => !$employee->is_active
        ]);

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'is_active' => $employee->is_active
            ]
        ]);
    }

    /**
     * Show pending users for approval
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('role', 'pending')->paginate(10);
        return view('employees.pending', compact('pendingUsers'));
    }

    /**
     * Approve user and assign role
     */
    public function approveUser(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,kasir,barista',
        ]);

        $user->update(['role' => $request->role]);

        // Send notification to user about approval
        NotificationService::notifyUserApproved($user, $request->role);

        return redirect()->route('employees.pending')
            ->with('success', 'User berhasil disetujui dan ditetapkan sebagai ' . $request->role);
    }

    /**
     * Reject user registration
     */
    public function rejectUser(User $user)
    {
        // Send notification to user about rejection
        NotificationService::notifyUserRejected($user);

        $user->delete();

        return redirect()->route('employees.pending')
            ->with('success', 'User registration rejected.');
    }
}