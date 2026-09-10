<?php

namespace App\Services\VTU;

use App\Services\VTU\Providers\MockVTUProvider;

class VTUService
{
    protected VTUProviderInterface $provider;

    protected array $providers = [];
    protected VTUProviderInterface $primaryProvider;

    public function __construct()
    {
        $defaultDriver = app()->environment('testing') ? 'mock' : 'bilalsada';
        $primaryDriverKey = strtolower(\App\Models\SystemSetting::get('active_vtu_provider', $defaultDriver));

        $allDrivers = [
            'bilalsada' => new \App\Services\VTU\Providers\BilalsadaProvider(),
            'alrahuz'   => new \App\Services\VTU\Providers\AlrahuzProvider(),
            'n3tdata'   => new \App\Services\VTU\Providers\N3tDataProvider(),
            'superjara' => new \App\Services\VTU\Providers\SuperjaraProvider(),
            'mock'      => new MockVTUProvider(),
        ];

        // 1. Build list of active enabled providers
        $activeList = [];
        
        // Put primary driver first if enabled
        if (($allDrivers[$primaryDriverKey] ?? null) && \App\Models\SystemSetting::get("provider_{$primaryDriverKey}_status", 'enabled') === 'enabled') {
            $activeList[] = $allDrivers[$primaryDriverKey];
        }

        // Add other enabled drivers as fallback options
        foreach ($allDrivers as $key => $inst) {
            if ($key !== $primaryDriverKey && \App\Models\SystemSetting::get("provider_{$key}_status", 'enabled') === 'enabled') {
                $activeList[] = $inst;
            }
        }

        // Default to Bilalsada API driver if no provider is explicitly enabled
        if (empty($activeList)) {
            $activeList[] = $allDrivers[$primaryDriverKey] ?? new \App\Services\VTU\Providers\BilalsadaProvider();
        }

        $this->providers = $activeList;
        $this->primaryProvider = $activeList[0];
    }

    public function getProvider(): VTUProviderInterface
    {
        return $this->primaryProvider;
    }

    public function purchaseAirtime(string $network, string $phone, float $amount, string $reference): array
    {
        $lastResult = ['success' => false, 'status' => 'failed', 'message' => 'No active provider available'];

        foreach ($this->providers as $p) {
            $lastResult = $p->purchaseAirtime($network, $phone, $amount, $reference);
            if (!empty($lastResult['success']) && $lastResult['status'] === 'successful') {
                return $lastResult;
            }
        }

        return $lastResult;
    }

    public function purchaseData(string $network, string $planCode, string $phone, string $reference): array
    {
        $lastResult = ['success' => false, 'status' => 'failed', 'message' => 'No active provider available'];

        foreach ($this->providers as $p) {
            $lastResult = $p->purchaseData($network, $planCode, $phone, $reference);
            if (!empty($lastResult['success']) && $lastResult['status'] === 'successful') {
                return $lastResult;
            }
        }

        return $lastResult;
    }

    public function verifyMeter(string $discoCode, string $meterNumber, string $type = 'prepaid'): array
    {
        return $this->primaryProvider->verifyMeter($discoCode, $meterNumber, $type);
    }

    public function payElectricity(string $discoCode, string $meterNumber, string $type, float $amount, string $phone, string $reference): array
    {
        $lastResult = ['success' => false, 'status' => 'failed', 'message' => 'No active provider available'];

        foreach ($this->providers as $p) {
            $lastResult = $p->payElectricity($discoCode, $meterNumber, $type, $amount, $phone, $reference);
            if (!empty($lastResult['success']) && $lastResult['status'] === 'successful') {
                return $lastResult;
            }
        }

        return $lastResult;
    }

    public function verifySmartcard(string $provider, string $smartcardNumber): array
    {
        return $this->primaryProvider->verifySmartcard($provider, $smartcardNumber);
    }

    public function payCable(string $provider, string $planCode, string $smartcardNumber, string $phone, string $reference): array
    {
        $lastResult = ['success' => false, 'status' => 'failed', 'message' => 'No active provider available'];

        foreach ($this->providers as $p) {
            $lastResult = $p->payCable($provider, $planCode, $smartcardNumber, $phone, $reference);
            if (!empty($lastResult['success']) && $lastResult['status'] === 'successful') {
                return $lastResult;
            }
        }

        return $lastResult;
    }

    public function checkStatus(string $reference, ?string $providerReference = null): array
    {
        return $this->primaryProvider->checkStatus($reference, $providerReference);
    }
}
