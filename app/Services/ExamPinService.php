<?php

namespace App\Services;

use App\Models\ExamPackage;
use App\Models\ExamPinTransaction;
use App\Models\SystemSetting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamPinService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function purchase(User $user, string $examCode, int $quantity, string $pin): ExamPinTransaction
    {
        if (!$user->verifyPin($pin)) {
            throw new Exception("Invalid 4-digit transaction PIN.");
        }

        $package = ExamPackage::where('code', strtoupper($examCode))->where('status', 'active')->first();
        if (!$package) {
            throw new Exception("Selected examination package is currently unavailable.");
        }

        if ($quantity < 1 || $quantity > 20) {
            throw new Exception("Quantity must be between 1 and 20 PINs per transaction.");
        }

        $unitPrice = $package->getPriceForTier($user->tier);
        $totalAmount = $unitPrice * $quantity;

        return DB::transaction(function () use ($user, $package, $quantity, $unitPrice, $totalAmount) {
            $reference = 'EXAM-' . strtoupper(Str::random(10));

            // 1. Debit Wallet
            $this->walletService->debit(
                $user,
                $totalAmount,
                "Purchase of {$quantity}x {$package->name}",
                $reference,
                ['type' => 'exam_pin']
            );

            // 2. Generate Realistic Exam PINs & Serial Numbers
            $pinsData = [];
            for ($i = 0; $i < $quantity; $i++) {
                $pinsData[] = [
                    'serial' => strtoupper($package->code) . '-' . date('Y') . '-' . rand(10000000, 99999999),
                    'pin' => rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999),
                ];
            }

            // 3. Create Exam PIN Transaction
            $transaction = ExamPinTransaction::create([
                'user_id' => $user->id,
                'exam_code' => $package->code,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'pins_data' => $pinsData,
                'reference' => $reference,
                'status' => 'successful',
            ]);

            // 4. Create Notification
            $user->appNotifications()->create([
                'title' => 'Exam PINs Delivered',
                'message' => "Your {$quantity}x {$package->name} have been generated and are ready for use. Ref: {$reference}",
                'type' => 'success',
            ]);

            return $transaction;
        });
    }
}
