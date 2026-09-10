<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::withCount('usages')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%");
        }

        $coupons = $query->paginate(15)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'service_type' => ['required', 'in:all,airtime,data,electricity,cable'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        $validated['status'] = 'active';

        $coupon = Coupon::create($validated);

        AuditLog::record(
            'coupon_created',
            "Admin " . Auth::user()->email . " created new coupon code {$coupon->code} with {$coupon->discount_type} discount of {$coupon->discount_value}",
            Auth::id()
        );

        return back()->with('success', "Coupon code '{$coupon->code}' created successfully.");
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->status = ($coupon->status === 'active') ? 'disabled' : 'active';
        $coupon->save();

        AuditLog::record(
            'coupon_toggled',
            "Admin " . Auth::user()->email . " toggled coupon code {$coupon->code} to {$coupon->status}",
            Auth::id()
        );

        return back()->with('success', "Coupon status updated to {$coupon->status}.");
    }

    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->delete();

        AuditLog::record(
            'coupon_deleted',
            "Admin " . Auth::user()->email . " deleted coupon code {$code}",
            Auth::id()
        );

        return back()->with('success', "Coupon code '{$code}' deleted successfully.");
    }
}
