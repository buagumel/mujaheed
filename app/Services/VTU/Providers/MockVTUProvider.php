<?php

namespace App\Services\VTU\Providers;

use App\Services\VTU\VTUProviderInterface;
use Illuminate\Support\Str;

class MockVTUProvider implements VTUProviderInterface
{
    public function purchaseAirtime(string $network, string $phone, float $amount, string $reference): array
    {
        // Simulate failure trigger if phone number ends with 9999
        if (str_ends_with($phone, '9999')) {
            return [
                'success' => false,
                'status' => 'failed',
                'provider_reference' => null,
                'cost_price' => 0.00,
                'message' => 'Simulated Network Failure: Destination number unavailable.',
                'raw_response' => ['error_code' => 'MOCK_FAIL', 'detail' => 'Simulated failure'],
            ];
        }

        $costPrice = round($amount * 0.97, 2); // 3% provider cost discount
        $providerRef = 'MOCK-AIRTIME-' . strtoupper(Str::random(10));

        return [
            'success' => true,
            'status' => 'successful',
            'provider_reference' => $providerRef,
            'cost_price' => $costPrice,
            'message' => "Airtime recharge of ₦{$amount} to {$phone} was successful.",
            'raw_response' => [
                'status' => 'success',
                'order_id' => $providerRef,
                'network' => $network,
                'phone' => $phone,
                'amount' => $amount,
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    public function purchaseData(string $network, string $planCode, string $phone, string $reference): array
    {
        if (str_ends_with($phone, '9999')) {
            return [
                'success' => false,
                'status' => 'failed',
                'provider_reference' => null,
                'cost_price' => 0.00,
                'message' => 'Simulated Provider Failure: Data routing temporary error.',
                'raw_response' => ['error_code' => 'MOCK_FAIL', 'detail' => 'Simulated data failure'],
            ];
        }

        $providerRef = 'MOCK-DATA-' . strtoupper(Str::random(10));

        return [
            'success' => true,
            'status' => 'successful',
            'provider_reference' => $providerRef,
            'cost_price' => 0.00,
            'message' => "Data bundle purchase ({$planCode}) for {$phone} was delivered successfully.",
            'raw_response' => [
                'status' => 'success',
                'order_id' => $providerRef,
                'network' => $network,
                'plan_code' => $planCode,
                'phone' => $phone,
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    public function verifyMeter(string $discoCode, string $meterNumber, string $type = 'prepaid'): array
    {
        if ($meterNumber === '00000000000') {
            return [
                'success' => false,
                'customer_name' => '',
                'address' => '',
                'message' => 'Invalid Meter Number.',
            ];
        }

        $names = [
            'EMMANUEL OKECHUKWU OKAFOR',
            'BABATUNDE RASHEED ADELEKE',
            'CHINWE NGOZI NWOSU',
            'FATIMA ABUBAKAR BELLO',
            'OLUWASEUN DANIEL AJAYI',
        ];
        $hash = abs(crc32($meterNumber)) % count($names);

        return [
            'success' => true,
            'customer_name' => $names[$hash],
            'address' => 'Plot ' . (10 + ($hash * 5)) . ' Victoria Island / Ikeja District, Lagos',
            'message' => 'Meter verified successfully.',
        ];
    }

    public function payElectricity(string $discoCode, string $meterNumber, string $type, float $amount, string $phone, string $reference): array
    {
        if (str_ends_with($meterNumber, '9999')) {
            return [
                'success' => false,
                'status' => 'failed',
                'provider_reference' => null,
                'token' => null,
                'units' => null,
                'message' => 'Simulated DISCO service outage.',
                'raw_response' => ['error' => 'DISCO_TIMEOUT'],
            ];
        }

        $providerRef = 'MOCK-ELEC-' . strtoupper(Str::random(10));
        $token = null;
        $units = null;

        if (strtolower($type) === 'prepaid') {
            // Generate standard 20-digit token (e.g. 4839-2049-1830-4920-1938)
            $digits = str_pad((string) random_int(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT) . random_int(1000, 9999);
            $token = chunk_split($digits, 4, '-');
            $token = rtrim($token, '-');
            $units = number_format($amount / 68.5, 1) . ' kWh';
        }

        return [
            'success' => true,
            'status' => 'successful',
            'provider_reference' => $providerRef,
            'token' => $token,
            'units' => $units,
            'message' => "Electricity recharge of ₦{$amount} for meter {$meterNumber} successful.",
            'raw_response' => [
                'status' => 'success',
                'order_id' => $providerRef,
                'disco' => $discoCode,
                'meter' => $meterNumber,
                'type' => $type,
                'token' => $token,
                'units' => $units,
                'amount' => $amount,
            ],
        ];
    }

    public function verifySmartcard(string $provider, string $smartcardNumber): array
    {
        if ($smartcardNumber === '0000000000') {
            return [
                'success' => false,
                'customer_name' => '',
                'message' => 'Invalid Smartcard / IUC Number.',
            ];
        }

        $names = [
            'CHIBUZOR MICHAEL ABEGBUNDE',
            'AISHA MOHAMMED YUSUF',
            'OLAWALE SAMSON KUTI',
            'BLESSING JOY EKWUEME',
        ];
        $hash = abs(crc32($smartcardNumber)) % count($names);

        return [
            'success' => true,
            'customer_name' => $names[$hash],
            'message' => 'Smartcard / IUC Number verified successfully.',
        ];
    }

    public function payCable(string $provider, string $planCode, string $smartcardNumber, string $phone, string $reference): array
    {
        if (str_ends_with($smartcardNumber, '9999')) {
            return [
                'success' => false,
                'status' => 'failed',
                'provider_reference' => null,
                'cost_price' => 0.00,
                'message' => 'Simulated Cable Provider Gateway Timeout.',
                'raw_response' => ['error' => 'GATEWAY_TIMEOUT'],
            ];
        }

        $providerRef = 'MOCK-CABLE-' . strtoupper(Str::random(10));

        return [
            'success' => true,
            'status' => 'successful',
            'provider_reference' => $providerRef,
            'cost_price' => 0.00,
            'message' => "Subscription for {$provider} ({$planCode}) on IUC {$smartcardNumber} activated successfully.",
            'raw_response' => [
                'status' => 'success',
                'order_id' => $providerRef,
                'provider' => $provider,
                'plan' => $planCode,
                'smartcard' => $smartcardNumber,
            ],
        ];
    }

    public function checkStatus(string $reference, ?string $providerReference = null): array
    {
        return [
            'status' => 'successful',
            'message' => 'Transaction confirmed successful by provider.',
            'raw_response' => [
                'reference' => $reference,
                'provider_reference' => $providerReference,
                'status' => 'successful',
            ],
        ];
    }
}
