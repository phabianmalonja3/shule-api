<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PayPalController extends Controller
{
    public function paymentSuccess(Request $request)
{
    DB::beginTransaction();

    try {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->capturePaymentOrder($request->token);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            // Update payment record
            $payment = Payment::findOrFail($request->payment_id);
            $payment->update([
                'status' => 'Paid',
                'transaction_id' => $response['id'],
            ]);

            DB::commit();

            flash()->option('position', 'bottom-right')->success('Subscription successful!');
            return redirect()->route('parents.index');
        }

        throw new \Exception($response['message'] ?? 'Payment capture failed.');
    } catch (\Exception $e) {
        DB::rollBack();
        flash()->option('position', 'bottom-right')->error('Payment failed: ' . $e->getMessage());

        return redirect()->route('parents.index');
    }
}

public function paymentCancel(Request $request)
{
    flash()->option('position', 'bottom-right')->error('You canceled the transaction.');
    return back();
}


}
