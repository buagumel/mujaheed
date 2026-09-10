<?php

namespace App\Services\VTU;

interface VTUProviderInterface
{
    /**
     * Purchase Airtime
     *
     * @param string $network (MTN, AIRTEL, GLO, 9MOBILE)
     * @param string $phone
     * @param float $amount
     * @param string $reference
     * @return array ['success' => bool, 'status' => string, 'provider_reference' => string, 'cost_price' => float, 'message' => string, 'raw_response' => array]
     */
    public function purchaseAirtime(string $network, string $phone, float $amount, string $reference): array;

    /**
     * Purchase Data Bundle
     *
     * @param string $network
     * @param string $planCode
     * @param string $phone
     * @param string $reference
     * @return array ['success' => bool, 'status' => string, 'provider_reference' => string, 'cost_price' => float, 'message' => string, 'raw_response' => array]
     */
    public function purchaseData(string $network, string $planCode, string $phone, string $reference): array;

    /**
     * Verify Meter Number for Electricity
     *
     * @param string $discoCode (IKEDC, EKEDC, AEDC, etc.)
     * @param string $meterNumber
     * @param string $type (prepaid, postpaid)
     * @return array ['success' => bool, 'customer_name' => string, 'address' => string, 'message' => string]
     */
    public function verifyMeter(string $discoCode, string $meterNumber, string $type = 'prepaid'): array;

    /**
     * Pay Electricity Bill
     *
     * @param string $discoCode
     * @param string $meterNumber
     * @param string $type
     * @param float $amount
     * @param string $phone
     * @param string $reference
     * @return array ['success' => bool, 'status' => string, 'provider_reference' => string, 'token' => string|null, 'units' => string|null, 'message' => string, 'raw_response' => array]
     */
    public function payElectricity(string $discoCode, string $meterNumber, string $type, float $amount, string $phone, string $reference): array;

    /**
     * Verify Cable Smartcard / IUC Number
     *
     * @param string $provider (DSTV, GOTV, STARTIMES)
     * @param string $smartcardNumber
     * @return array ['success' => bool, 'customer_name' => string, 'message' => string]
     */
    public function verifySmartcard(string $provider, string $smartcardNumber): array;

    /**
     * Purchase / Renew Cable TV Subscription
     *
     * @param string $provider
     * @param string $planCode
     * @param string $smartcardNumber
     * @param string $phone
     * @param string $reference
     * @return array ['success' => bool, 'status' => string, 'provider_reference' => string, 'cost_price' => float, 'message' => string, 'raw_response' => array]
     */
    public function payCable(string $provider, string $planCode, string $smartcardNumber, string $phone, string $reference): array;

    /**
     * Query Transaction Status from Provider
     *
     * @param string $reference
     * @param string $providerReference
     * @return array ['status' => string, 'message' => string, 'raw_response' => array]
     */
    public function checkStatus(string $reference, ?string $providerReference = null): array;
}
