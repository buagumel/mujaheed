<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $wallet = $user->getOrCreateWallet();
        $transactions = $user->vtuTransactions()->latest()->take(10)->get();
        $walletTransactions = $user->walletTransactions()->latest()->take(10)->get();

        return view('admin.users.show', compact('user', 'wallet', 'transactions', 'walletTransactions'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => ['required', 'in:active,suspended,blocked'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own account status.');
        }

        $oldStatus = $user->status;
        $user->update(['status' => $request->status]);

        AuditLog::record(
            'user_status_update',
            "Admin changed status of {$user->email} from {$oldStatus} to {$request->status}. Reason: " . ($request->reason ?? 'None provided'),
            $user->id,
            ['old_status' => $oldStatus, 'new_status' => $request->status, 'admin_id' => Auth::id()]
        );

        return back()->with('success', "User status updated to {$request->status} successfully.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        AuditLog::record(
            'admin_user_password_reset',
            "Admin reset password for customer {$user->email}.",
            $user->id,
            ['admin_id' => Auth::guard('admin')->id()]
        );

        return back()->with('success', "Password for user {$user->name} ({$user->email}) reset successfully.");
    }
}
