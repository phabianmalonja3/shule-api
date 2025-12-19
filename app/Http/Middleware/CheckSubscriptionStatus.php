<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        $payment = Payment::where('parent_id', auth()->user()->parent->id)
        ->where('status', 'Paid')
        ->latest('subscription_end')
        ->first();

// Check if there's no payment record or the subscription has expired
if (!$payment || $payment->subscription_end <= now()) {
// If no payment or expired subscription, redirect to payment page
return redirect()->route('subscription.status')->with('error', 'Your subscription has expired or is not paid.');
}
        return $next($request);
    }
}
