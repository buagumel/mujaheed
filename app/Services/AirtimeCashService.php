<?php

namespace App\Services;

use App\Models\AirtimeCashRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AirtimeCashService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public static function getRate(string $network): float
    {
        return match (strtoupper($network)) {
            'MTN' => (float) SystemSetting::get('airtime_cash_rate_mtn', 80.00),
            'AIRTEL' => (float) SystemSetting::get('airtime_cash_rate_airtel', 75.00),
            'GLO' => (float) SystemSetting::get('airtime_cash_rate_glo', 70.00),
            '9MOBILE' => (float) SystemSetting::get('airtime_cash_rate_9mobile', 70.00),
            default => 70.00,
        };
    }

    public static function getReceiverPhone(string $network): string
    {
        return match (strtoupper($network)) {
            'MTN' => (string) SystemSetting::get('airtime_cash_receiver_mtn', '08031234567'),
            'AIRTEL' => (string) SystemSetting::get('airtime_cash_receiver_airtel', '08021234567'),
            'GLO' => (string) SystemSetting::get('airtime_cash_receiver_glo', '08051234567'),
            '9MOBILE' => (string) SystemSetting::get('airtime_cash_receiver_9mobile', '08091234567'),
            default => '08031234567',
        };
    }

    public static function getUssdInstruction(string $network, float $amount, string $receiver): string
    {
        return match (strtoupper($network)) {
            'MTN' => "*600*{$receiver}*{$amount}*PIN# (Default MTN Transfer PIN is 0000)",
            'AIRTEL' => "*432*1*{$receiver}*{$amount}*PIN# (Default Airtel PIN is 1234)",
            'GLO' => "*131*{$receiver}*{$amount}*PIN# (Default Glo PIN is 00000)",
            '9MOBILE' => "*223*PIN*{$amount}*{$receiver}# (Default 9mobile PIN is 0000)",
            default => "Transfer airtime to {$receiver}",
        };
    }

    public function submitRequest(
        User $user,
        string $network,
        float $amount,
        string $senderPhone,
        string $payoutMethod = 'wallet',
        ?string $bankName = null,
        ?string $accountNumber = null,
        ?string $accountName = null
    ): AirtimeCashRequest {
        $network = strtoupper($network);
        if (!in_array($network, ['MTN', 'AIRTEL', 'GLO', '9MOBILE'])) {
            throw new Exception("Invalid network specified.");
        }

        if ($amount < 500 || $amount > 50000) {
            throw new Exception("Airtime conversion amount must be between ₦500 and ₦50,000.");
        }

        $rate = self::getRate($network);
        $amountToReceive = ($amount * $rate) / 100;
        $receiverPhone = self::getReceiverPhone($network);
        $reference = 'A2C-' . strtoupper(Str::random(10));

        $req = AirtimeCashRequest::create([
            'user_id' => $user->id,
            'network' => $network,
            'amount' => $amount,
            'exchange_rate_percent' => $rate,
            'amount_to_receive' => $amountToReceive,
            'sender_phone' => $senderPhone,
            'receiver_phone' => $receiverPhone,
            'payout_method' => $payoutMethod,
            'bank_name' => $bankName,
            'bank_account_number' => $accountNumber,
            'bank_account_name' => $accountName,
            'reference' => $reference,
            'status' => 'pending',
        ]);

        $user->appNotifications()->create([
            'title' => 'Airtime to Cash Submitted',
            'message' => "Your conversion request of ₦" . number_format($amount, 2) . " {$network} airtime for ₦" . number_format($amountToReceive, 2) . " is being processed.",
            'type' => 'info',
        ]);

        return $req;
    }

    public function approveRequest(AirtimeCashRequest $request, string $adminNote = 'Airtime received and approved.'): void
    {
        if ($request->status !== 'pending') {
            throw new Exception("This request has already been {$request->status}.");
        }

        DB::transaction(function () use ($request, $adminNote) {
            $user = $request->user;

            if ($request->payout_method === 'wallet') {
                $this->walletService->credit(
                    $user,
                    (float) $request->amount_to_receive,
                    "Airtime to Cash ({$request->network} ₦" . number_format($request->amount, 2) . " converted)",
                    $request->reference,
                    ['type' => 'airtime_cash']
                );
            }

            $request->update([
                'status' => 'approved',
                'admin_note' => $adminNote,
            ]);

            $user->appNotifications()->create([
                'title' => '✅ Airtime Conversion Approved!',
                'message' => "₦" . number_format($request->amount_to_receive, 2) . " has been credited for your {$request->network} airtime conversion.",
                'type' => 'success',
            ]);
        });
    }

    public function rejectRequest(AirtimeCashRequest $request, string $reason = 'Airtime not received or wrong amount.'): void
    {
        if ($request->status !== 'pending') {
            throw new Exception("This request has already been {$request->status}.");
        }

        $request->update([
            'status' => 'rejected',
            'admin_note' => $reason,
        ]);

        $request->user->appNotifications()->create([
            'title' => '❌ Airtime Conversion Declined',
            'message' => "Your Airtime to Cash request was declined: {$reason}",
            'type' => 'error',
        ]);
    }
}
