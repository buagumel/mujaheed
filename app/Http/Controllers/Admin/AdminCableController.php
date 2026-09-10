<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CablePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCableController extends Controller
{
    public function index(Request $request)
    {
        $query = CablePlan::orderBy('provider')->orderBy('selling_price');

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        $plans = $query->paginate(20)->withQueryString();

        return view('admin.cable.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => ['required', 'in:DSTV,GOTV,STARTIMES'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:cable_plans,code'],
            'provider_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $plan = CablePlan::create($validated);

        AuditLog::record(
            'cable_plan_created',
            "Admin created cable plan: {$plan->provider} - {$plan->name}.",
            Auth::id(),
            $validated
        );

        return back()->with('success', 'Cable package created successfully.');
    }

    public function update(Request $request, CablePlan $plan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:cable_plans,code,' . $plan->id],
            'provider_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $plan->update($validated);

        AuditLog::record(
            'cable_plan_updated',
            "Admin updated cable plan: {$plan->provider} - {$plan->name}.",
            Auth::id(),
            $validated
        );

        return back()->with('success', 'Cable package updated successfully.');
    }
}
