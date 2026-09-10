<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Models\ExamPinTransaction;
use Illuminate\Http\Request;

class AdminExamPinController extends Controller
{
    public function index()
    {
        $packages = ExamPackage::all();
        $transactions = ExamPinTransaction::with('user')->latest()->paginate(15);
        $totalSales = ExamPinTransaction::where('status', 'successful')->sum('total_amount');
        $totalPinsSold = ExamPinTransaction::where('status', 'successful')->sum('quantity');

        return view('admin.exam_pins.index', compact('packages', 'transactions', 'totalSales', 'totalPinsSold'));
    }

    public function updatePrice(Request $request, int $id)
    {
        $request->validate([
            'standard_price' => ['required', 'numeric', 'min:0'],
            'reseller_price' => ['required', 'numeric', 'min:0'],
            'vip_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $package = ExamPackage::findOrFail($id);
        $package->update($request->only('standard_price', 'reseller_price', 'vip_price', 'status'));

        return back()->with('success', "{$package->name} pricing updated successfully!");
    }
}
