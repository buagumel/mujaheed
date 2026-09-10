<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PayrantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayrantWebhookController extends Controller
{
    protected PayrantService $payrantService;

    public function __construct(PayrantService $payrantService)
    {
        $this->payrantService = $payrantService;
    }

    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-Payrant-Signature') ?? $request->header('x-payrant-signature');
        $rawPayload = $request->getContent();
        $payload = $request->json()->all();

        Log::info('Payrant Inbound Webhook Received', [
            'signature_present' => !empty($signature),
            'payload' => $payload,
        ]);

        if (empty($payload)) {
            $payload = json_decode($rawPayload, true) ?? [];
        }

        $result = $this->payrantService->processWebhook($payload, $rawPayload, $signature);

        $statusCode = $result['code'] ?? 200;

        return response()->json([
            'status' => 'received',
            'message' => $result['message'] ?? 'Webhook processed successfully',
            'result' => $result,
        ], $statusCode == 401 ? 401 : 200); // Always return 200 unless unauthorized
    }
}
