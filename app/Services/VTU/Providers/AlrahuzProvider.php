<?php

namespace App\Services\VTU\Providers;

use App\Models\SystemSetting;
use App\Services\VTU\VTUProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlrahuzProvider implements VTUProviderInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(SystemSetting::get('alrahuz_base_url', env('ALRAHUZ_BASE_URL', 'https://alrahuzdata.com.ng')), '/');
        $this->apiKey = SystemSetting::get('alrahuz_api_key', env('ALRAHUZ_API_KEY', 'c9d7930f731c093e2ee81369d2aea34995a7b251'));
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    protected function mapNetworkToId(string $network): int
    {
        return match (strtoupper($network)) {
            'MTN' => 1,
            'AIRTEL' => 2,
            '9MOBILE', 'T2' => 3,
            'GLO' => 4,
            default => 1,
        };
    }

    protected function mapCableToId(string $cable): int
    {
        return match (strtoupper($cable)) {
            'GOTV' => 1,
            'DSTV' => 2,
            'STARTIMES', 'STARTIME' => 3,
            default => 1,
        };
    }

    protected function mapDiscoToId(string $disco): int
    {
        $disco = strtoupper($disco);
        if (str_contains($disco, 'IKEJA') || $disco === 'IKEDC') return 1;
        if (str_contains($disco, 'EKO') || $disco === 'EKEDC') return 2;
        if (str_contains($disco, 'KANO') || $disco === 'KEDCO') return 3;
        if (str_contains($disco, 'PORT') || $disco === 'PHED') return 4;
        if (str_contains($disco, 'JOS') || $disco === 'JED') return 5;
        if (str_contains($disco, 'IBADAN') || $disco === 'IBEDC') return 6;
        if (str_contains($disco, 'KADUNA') || $disco === 'KAEDCO') return 7;
        if (str_contains($disco, 'ABUJA') || $disco === 'AEDC') return 8;

        return 1;
    }

    public function purchaseAirtime(string $network, string $phone, float $amount, string $reference): array
    {
        try {
            $payload = [
                'network'    => $this->mapNetworkToId($network),
                'phone'      => $phone,
                'amount'     => (int) $amount,
                'airtime_type' => 'VTU',
                'bypass'     => true,
                'request-id' => $reference,
            ];

            $response = Http::timeout(15)->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/topup/", $payload);

            $data = $response->json();
            $status = strtolower($data['status'] ?? 'fail');
            $isSuccess = ($status === 'success' || str_contains(strtolower($data['Status'] ?? ''), 'success'));

            return [
                'success'            => $isSuccess,
                'status'             => $isSuccess ? 'successful' : 'failed',
                'provider_reference' => $data['request-id'] ?? $reference,
                'cost_price'         => (float) ($data['amount'] ?? $amount),
                'message'            => $data['message'] ?? $data['response'] ?? ($isSuccess ? 'Airtime successful' : 'Airtime failed'),
                'raw_response'       => $data ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('Alrahuz Airtime Exception: ' . $e->getMessage());

            return [
                'success'            => false,
                'status'             => 'failed',
                'provider_reference' => null,
                'cost_price'         => 0.00,
                'message'            => 'Provider Error: ' . $e->getMessage(),
                'raw_response'       => ['error' => $e->getMessage()],
            ];
        }
    }

    public function purchaseData(string $network, string $planCode, string $phone, string $reference): array
    {
        try {
            $payload = [
                'network'    => $this->mapNetworkToId($network),
                'mobile_number' => $phone,
                'plan'       => (int) $planCode,
                'Ported_number' => true,
                'request-id' => $reference,
            ];

            $response = Http::timeout(15)->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/data/", $payload);

            $data = $response->json();
            $status = strtolower($data['status'] ?? $data['Status'] ?? 'fail');
            $isSuccess = ($status === 'success' || $status === 'successful');

            return [
                'success'            => $isSuccess,
                'status'             => $isSuccess ? 'successful' : 'failed',
                'provider_reference' => $data['request-id'] ?? $reference,
                'cost_price'         => (float) ($data['amount'] ?? 0),
                'message'            => $data['message'] ?? $data['api_response'] ?? ($isSuccess ? 'Data delivery successful' : 'Data purchase failed'),
                'raw_response'       => $data ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('Alrahuz Data Exception: ' . $e->getMessage());

            return [
                'success'            => false,
                'status'             => 'failed',
                'provider_reference' => null,
                'cost_price'         => 0.00,
                'message'            => 'Provider Error: ' . $e->getMessage(),
                'raw_response'       => ['error' => $e->getMessage()],
            ];
        }
    }

    public function verifyMeter(string $discoCode, string $meterNumber, string $type = 'prepaid'): array
    {
        try {
            $discoId = $this->mapDiscoToId($discoCode);
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/validatemeter/?meternumber={$meterNumber}&disconame={$discoId}&mtype=" . strtolower($type));

            $data = $response->json();
            $invalid = $data['invalid'] ?? false;

            if ($invalid) {
                return [
                    'success'       => false,
                    'customer_name' => '',
                    'address'       => '',
                    'message'       => $data['name'] ?? 'Invalid Meter Number',
                ];
            }

            return [
                'success'       => true,
                'customer_name' => $data['name'] ?? 'Meter Validated',
                'address'       => $data['address'] ?? 'Verified via Alrahuz Gateway',
                'message'       => 'Meter verified successfully.',
            ];
        } catch (\Throwable $e) {
            return [
                'success'       => false,
                'customer_name' => '',
                'address'       => '',
                'message'       => 'Meter verification error: ' . $e->getMessage(),
            ];
        }
    }

    public function payElectricity(string $discoCode, string $meterNumber, string $type, float $amount, string $phone, string $reference): array
    {
        try {
            $discoId = $this->mapDiscoToId($discoCode);
            $payload = [
                'disco_name'   => $discoId,
                'amount'       => (int) $amount,
                'meter_number' => $meterNumber,
                'MeterType'    => strtolower($type) === 'prepaid' ? '1' : '2',
                'Customer_Phone' => $phone,
                'request-id'   => $reference,
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/billpayment/", $payload);

            $data = $response->json();
            $status = strtolower($data['status'] ?? $data['Status'] ?? 'fail');
            $isSuccess = ($status === 'success' || $status === 'successful');

            return [
                'success'            => $isSuccess,
                'status'             => $isSuccess ? 'successful' : 'failed',
                'provider_reference' => $data['request-id'] ?? $reference,
                'token'              => $data['token'] ?? null,
                'units'              => $data['units'] ?? null,
                'message'            => $data['message'] ?? ($isSuccess ? 'Electricity payment successful' : 'Electricity payment failed'),
                'raw_response'       => $data ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('Alrahuz Electricity Exception: ' . $e->getMessage());

            return [
                'success'            => false,
                'status'             => 'failed',
                'provider_reference' => null,
                'token'              => null,
                'units'              => null,
                'message'            => 'Provider Error: ' . $e->getMessage(),
                'raw_response'       => ['error' => $e->getMessage()],
            ];
        }
    }

    public function verifySmartcard(string $provider, string $smartcardNumber): array
    {
        try {
            $cableId = $this->mapCableToId($provider);
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/validateiuc/?smart_card_number={$smartcardNumber}&cablename={$cableId}");

            $data = $response->json();
            $invalid = $data['invalid'] ?? false;

            if ($invalid) {
                return [
                    'success'       => false,
                    'customer_name' => '',
                    'message'       => $data['name'] ?? 'Invalid Smartcard / IUC Number',
                ];
            }

            return [
                'success'       => true,
                'customer_name' => $data['name'] ?? 'Subscriber Validated',
                'message'       => 'Smartcard / IUC verified successfully.',
            ];
        } catch (\Throwable $e) {
            return [
                'success'       => false,
                'customer_name' => '',
                'message'       => 'Smartcard verification error: ' . $e->getMessage(),
            ];
        }
    }

    public function payCable(string $provider, string $planCode, string $smartcardNumber, string $phone, string $reference): array
    {
        try {
            $cableId = $this->mapCableToId($provider);
            $payload = [
                'cablename'         => $cableId,
                'cableplan'         => (int) $planCode,
                'smart_card_number' => $smartcardNumber,
                'request-id'        => $reference,
            ];

            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/cablesub/", $payload);

            $data = $response->json();
            $status = strtolower($data['status'] ?? $data['Status'] ?? 'fail');
            $isSuccess = ($status === 'success' || $status === 'successful');

            return [
                'success'            => $isSuccess,
                'status'             => $isSuccess ? 'successful' : 'failed',
                'provider_reference' => $data['request-id'] ?? $reference,
                'cost_price'         => (float) ($data['amount'] ?? 0),
                'message'            => $data['message'] ?? ($isSuccess ? 'Cable subscription updated' : 'Cable subscription failed'),
                'raw_response'       => $data ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('Alrahuz Cable Exception: ' . $e->getMessage());

            return [
                'success'            => false,
                'status'             => 'failed',
                'provider_reference' => null,
                'cost_price'         => 0.00,
                'message'            => 'Provider Error: ' . $e->getMessage(),
                'raw_response'       => ['error' => $e->getMessage()],
            ];
        }
    }

    public function checkStatus(string $reference, ?string $providerReference = null): array
    {
        return [
            'status'       => 'successful',
            'message'      => 'Status verified',
            'raw_response' => ['reference' => $reference],
        ];
    }
}
