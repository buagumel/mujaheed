<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CablePlan;
use App\Models\DataPlan;
use App\Models\ElectricityProvider;
use App\Models\NetworkSetting;
use App\Models\VtuTransaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPricingController extends Controller
{
    public function index(Request $request)
    {
        $networks = NetworkSetting::all();
        $dataPlans = DataPlan::orderBy('network')->orderBy('selling_price')->get();
        $cablePlans = CablePlan::orderBy('provider')->orderBy('selling_price')->get();
        $electricity = ElectricityProvider::all();

        // Financial Profit & Loss Analytics
        $period = $request->get('period', 'all');
        $txQuery = VtuTransaction::where('status', 'successful');

        if ($period === 'today') {
            $txQuery->whereDate('created_at', today());
        } elseif ($period === 'week') {
            $txQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $txQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        $totalSales = (float) $txQuery->sum('amount');
        $totalCost = (float) $txQuery->sum('cost_price');
        $netProfit = max(0, $totalSales - $totalCost);
        $profitMargin = $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 2) : 0;

        // Profit breakdown by service
        $services = ['data', 'airtime', 'electricity', 'cable', 'exam_pins', 'airtime_cash'];
        $profitByService = [];

        foreach ($services as $srv) {
            $srvQuery = VtuTransaction::where('status', 'successful')->where('service_type', $srv);
            if ($period === 'today') {
                $srvQuery->whereDate('created_at', today());
            } elseif ($period === 'week') {
                $srvQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($period === 'month') {
                $srvQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            }

            $sSales = (float) $srvQuery->sum('amount');
            $sCost = (float) $srvQuery->sum('cost_price');
            $sCount = $srvQuery->count();
            $sProfit = max(0, $sSales - $sCost);

            $profitByService[$srv] = [
                'count' => $sCount,
                'sales' => $sSales,
                'cost' => $sCost,
                'profit' => $sProfit,
                'margin' => $sSales > 0 ? round(($sProfit / $sSales) * 100, 1) : 0,
            ];
        }

        return view('admin.pricing.index', compact(
            'networks',
            'dataPlans',
            'cablePlans',
            'electricity',
            'totalSales',
            'totalCost',
            'netProfit',
            'profitMargin',
            'profitByService',
            'period'
        ));
    }

    public function exportProfitCsv(Request $request): StreamedResponse
    {
        $transactions = VtuTransaction::with('user')
            ->where('status', 'successful')
            ->latest('created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="financial_profit_report_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Reference',
                'Date & Time',
                'Customer Name',
                'Customer Email',
                'Service Type',
                'Provider',
                'Recipient / Identifier',
                'Selling Price (NGN)',
                'Cost Price (NGN)',
                'Net Profit (NGN)',
                'Profit Margin (%)',
                'Status'
            ]);

            foreach ($transactions as $tx) {
                $selling = (float) $tx->amount;
                $cost = (float) $tx->cost_price;
                $profit = max(0, $selling - $cost);
                $margin = $selling > 0 ? round(($profit / $selling) * 100, 2) : 0;

                fputcsv($handle, [
                    $tx->reference,
                    $tx->created_at->format('Y-m-d H:i:s'),
                    $tx->user->name ?? 'N/A',
                    $tx->user->email ?? 'N/A',
                    strtoupper($tx->service_type),
                    $tx->provider,
                    $tx->recipient,
                    number_format($selling, 2, '.', ''),
                    number_format($cost, 2, '.', ''),
                    number_format($profit, 2, '.', ''),
                    $margin . '%',
                    ucfirst($tx->status)
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
