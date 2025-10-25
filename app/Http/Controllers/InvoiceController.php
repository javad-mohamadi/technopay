<?php

namespace App\Http\Controllers;

use App\DTOs\Payment\PayInvoiceDTO;
use App\Http\Requests\PayInvoiceValidation;
use App\Models\Invoice;
use App\Services\Interfaces\PaymentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvoiceController extends Controller
{
    public function __construct(protected PaymentServiceInterface $paymentService) {}

    public function requestPayment(Invoice $invoice): JsonResponse
    {
        try {
            $this->paymentService->initiatePayment(auth()->id(), $invoice);

            return response()->json([
                'message' => 'OTP has been sent successfully. It is valid for 5 minutes.',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            Log::error("OTP request failed for invoice {$invoice->id}: ".$e->getMessage());

            return response()->json(['message' => 'Could not process payment request.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function payInvoice(PayInvoiceValidation $request)
    {
        try {
            $transaction = $this->paymentService->pay(PayInvoiceDTO::getFromRequest($request));

            return response()->json([
                'message' => 'Payment successful!',
                'transaction_reference' => $transaction->id,
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            Log::critical('Unexpected payment error.', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }

    }
}
