<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DataPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDataController extends Controller
{
    public function index(Request $request)
    {
        $query = DataPlan::latest();

        if ($request->filled('network')) {
            $query->where('network', $request->network);
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plans = $query->paginate(20)->withQueryString();

        return view('admin.data.index', compact('plans'));
    }

    public function getProviderPlans(Request $request)
    {
        $provider = $request->query('provider', 'bilalsada');
        $plans = DataPlan::where('provider', $provider)->orderBy('network')->orderBy('selling_price')->get();

        return response()->json([
            'status' => 'success',
            'provider' => $provider,
            'plans' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'network' => ['required', 'in:MTN,AIRTEL,GLO,9MOBILE'],
            'provider' => ['required', 'string', 'in:bilalsada,alrahuz,n3tdata,superjara,mock'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:data_plans,code'],
            'type' => ['required', 'string'],
            'size' => ['required', 'string'],
            'validity' => ['required', 'string'],
            'provider_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $plan = DataPlan::create($validated);

        AuditLog::record(
            'data_plan_created',
            "Admin created data plan: {$plan->network} ({$plan->provider}) - {$plan->name}.",
            Auth::id(),
            $validated
        );

        return back()->with('success', 'Data plan created successfully.');
    }

    public function update(Request $request, DataPlan $plan)
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'in:bilalsada,alrahuz,n3tdata,superjara,mock'],
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:data_plans,code,' . $plan->id],
            'type' => ['required', 'string'],
            'size' => ['required', 'string'],
            'validity' => ['required', 'string'],
            'provider_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $plan->update($validated);

        AuditLog::record(
            'data_plan_updated',
            "Admin updated data plan: {$plan->network} ({$plan->provider}) - {$plan->name}.",
            Auth::id(),
            $validated
        );

        return back()->with('success', 'Data plan updated successfully.');
    }

    public function destroy(DataPlan $plan)
    {
        $name = "{$plan->network} - {$plan->name}";
        $plan->delete();

        AuditLog::record(
            'data_plan_deleted',
            "Admin deleted data plan: {$name}.",
            Auth::id()
        );

        return back()->with('success', 'Data plan deleted successfully.');
    }
}
