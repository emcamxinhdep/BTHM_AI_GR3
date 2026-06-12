<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Appointment;
use App\Models\clients\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private string $momoEndpoint = 'https://test-payment.momo.vn/v2/gateway/api/create';

    public function createMomo(Request $request)
    {
        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('patient_id', session('patient_id'))
            ->firstOrFail();

        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey   = env('MOMO_ACCESS_KEY');
        $secretKey   = env('MOMO_SECRET_KEY');
        $orderId     = 'DC_' . $appointment->id . '_' . time();
        $requestId   = Str::uuid()->toString();
        $amount      = (string) $appointment->fee;
        $orderInfo   = 'DoctorCam - Thanh toán lịch hẹn #' . $appointment->id;
        $returnUrl   = route('payment.momo.return');
        $notifyUrl   = route('payment.momo.notify');
        $requestType = 'paymentCode';
        $extraData   = base64_encode(json_encode(['appointment_id' => $appointment->id]));

        $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}"
            . "&ipnUrl={$notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}&redirectUrl={$returnUrl}"
            . "&requestId={$requestId}&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $body = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $response = \Illuminate\Support\Facades\Http::post($this->momoEndpoint, $body);
        $result   = $response->json();

        if (isset($result['resultCode']) && $result['resultCode'] === 0) {
            // Lưu payment record
            Payment::create([
                'appointment_id' => $appointment->id,
                'patient_id'     => session('patient_id'),
                'amount'         => $appointment->fee,
                'method'         => 'momo',
                'order_id'       => $orderId,
                'request_id'     => $requestId,
                'status'         => 'pending',
            ]);

            return redirect($result['payUrl']);
        }

        return back()->with('error', 'Không thể kết nối MoMo. Vui lòng thử lại.');
    }

    public function momoReturn(Request $request)
    {
        if ($request->resultCode == 0) {
            $extraData   = json_decode(base64_decode($request->extraData), true);
            $appointment = Appointment::find($extraData['appointment_id']);

            if ($appointment) {
                $appointment->update([
                    'payment_status'         => 'paid',
                    'payment_transaction_id' => $request->transId,
                ]);

                Payment::where('order_id', $request->orderId)
                    ->update([
                        'transaction_id' => $request->transId,
                        'status'         => 'success',
                        'raw_response'   => json_encode($request->all()),
                    ]);
            }

            return redirect()->route('appointments.index')
                ->with('success', 'Thanh toán MoMo thành công!');
        }

        return redirect()->route('appointments.index')
            ->with('error', 'Thanh toán thất bại hoặc bị hủy.');
    }

    public function momoNotify(Request $request)
    {
        \Log::info('MoMo IPN:', $request->all());

        if ($request->resultCode == 0) {
            $extraData   = json_decode(base64_decode($request->extraData), true);
            $appointment = Appointment::find($extraData['appointment_id']);

            if ($appointment) {
                $appointment->update([
                    'payment_status'         => 'paid',
                    'payment_transaction_id' => $request->transId,
                ]);
            }
        }

        return response()->json(['message' => 'ok']);
    }
}