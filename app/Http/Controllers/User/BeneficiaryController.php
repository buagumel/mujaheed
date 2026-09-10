<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Beneficiary::where('user_id', $user->id)->latest();

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        $beneficiaries = $query->get();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $beneficiaries,
            ]);
        }

        return view('user.beneficiaries.index', compact('beneficiaries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_type' => ['required', 'in:airtime,data,electricity,cable'],
            'identifier' => ['required', 'string', 'max:50'],
            'name_nickname' => ['nullable', 'string', 'max:100'],
            'network_or_disco' => ['nullable', 'string', 'max:50'],
        ]);

        $beneficiary = Beneficiary::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'service_type' => $validated['service_type'],
                'identifier' => $validated['identifier'],
            ],
            [
                'name_nickname' => $validated['name_nickname'],
                'network_or_disco' => $validated['network_or_disco'] ?? null,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Beneficiary saved successfully.',
                'data' => $beneficiary,
            ]);
        }

        return back()->with('success', 'Beneficiary saved successfully.');
    }

    public function destroy(Beneficiary $beneficiary)
    {
        if ($beneficiary->user_id !== Auth::id()) {
            abort(403);
        }

        $beneficiary->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Beneficiary removed successfully.',
            ]);
        }

        return back()->with('success', 'Beneficiary removed successfully.');
    }
}
