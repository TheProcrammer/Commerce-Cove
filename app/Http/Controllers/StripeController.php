<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Http\Resources\OrderViewResource;
use App\Mail\NewOrderMail;
use App\Mail\CheckoutCompleted;
use Illuminate\Support\Facades\Mail;

class StripeController extends Controller
{
    // Handles the success scenario after a Stripe payment
    //Validates the session ID, Checks ownership, Displays the success page.
    public function success(Request $request)
    {
        $user = auth()->user(); // Get the currently authenticated user
        $session_id = $request->get('session_id'); // Get the Stripe session ID from the request
        $orders = Order::where('stripe_session_id', $session_id)  // Find orders linked to the session ID
            ->get();
        // If no orders are found with the given session ID, show a 404 error
        if ($orders->count() === 0) {
            abort(404);
        }
        // Check if the order belongs to the authenticated user; if not, show a 403 error
        foreach ($orders as $order) {
            if ($order->user_id !== $user->id) {
                abort(403);
            }
        }
        // Render the success page with the orders, transformed by the OrderViewResource
        return Inertia::render('Stripe/Success', [
           'orders' => OrderViewResource::collection($orders)->collection->toArray(),
        ]);
    }

    public function failure()
    {

    }

    // Updates order commissions and vendor earnings based on Stripe transaction details.
    // Marks orders as paid, updates product stock, and removes purchased items from the user's cart.
    public function webhook(Request $request)
    {
        // Initialize Stripe client
        $stripe = new \Stripe\StripeClient(config('app.stripe_secret_key'));
        $endpoint_secret = config('app.stripe_webhook_secret');
        $payload = $request->getContent();
        $sig_header = request()->header('Stripe-Signature');
        $event = null;
        try {
            // Validate the webhook event from Stripe
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error($e);
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error($e);
            return response('Invalid payload', 400);
        }

        // For debugging
        Log::info('=================');
        Log::info('=================');
        Log::info($event->type);
        Log::info($event);

        switch ($event->type) {
            case 'charge.updated': // Handle charge update event
                $charge = $event->data->object;
                $transactionId = $charge['balance_transaction'];
                $paymentIntent = $charge['payment_intent'];
                $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId);

                // Get all orders related to the payment intent
                $orders = Order::where('payment_intent', $paymentIntent)
                    ->get();

                $totalAmount = $balanceTransaction['amount'];
                $stripeFee = 0;
                // Calculate Stripe fee
                foreach ($balanceTransaction['fee_details'] as $fee_detail) {
                    if ($fee_detail['type'] === 'stripe_fee') {
                        $stripeFee = $fee_detail['amount'];
                    }
                }
                $platformFeePercent = config('app.platform_fee_pct');

                // Calculate commissions and vendor subtotal for each order
                foreach ($orders as $order) {
                    $vendorShare = $order->total_price/$totalAmount;

                    $order->online_payment_commission = $vendorShare * $stripeFee;
                    $order->website_commission = ($order->total_price - $order->online_payment_commission) / 100;
                    $order->vendor_subtotal = $order->total_price - $order->online_payment_commission - $order->website_commission;
                    $order->save();

                    Mail::to($order->vendorUser)->send(new NewOrderMail($order));
                }
                Mail::to($order[0]->user)->send(new CheckoutCompleted($orders));

            case 'checkout.session.completed': // Handle successful checkout
                $session = $event->data->object;
                $pi = $session['payment_intent'];

                // Get all orders related to this checkout session
                $orders = Order::query()
                        ->with(['orderItems'])
                        ->where(['stripe_session_id' => $session['id']])
                        ->get();
                // Collect product IDs for removal from cart
                $productsToDeleteFromCart = [];

                // Update product stock levels
                foreach ($orders as $order) {
                    $order->payment_intent = $pi;
                    $order->status = OrderStatusEnum::Paid;
                    $order->save();

                    $productsToDeleteFromCart = [
                        ...$productsToDeleteFromCart,
                        ...$order->orderItems->map(fn($item) => $item->product_id)->toArray(),
                    ];

                    foreach ($order->orderItems as $orderItem) {
                        $options = $orderItem->variation_type_option_ids;
                        $product = $orderItem->product;

                        if ($options) {
                            sort($options);
                            $variation = $product->variations()
                                ->where('variation_type_option_ids', $options)
                                ->first();

                            if ($variation && $variation->quantity != null) {
                                $product->quantity -= $orderItem->quantity;
                                $product->save();
                        }
                    } else if ($product->quantity != null) {
                        $product->quantity -= $orderItem->quantity;
                        $product->save();
                    }
                }
            }
             // Remove purchased items from user's cart
            CartItem::query()
                ->where('user_id', $order->user_id)
                ->whereIn('product_id', $productsToDeleteFromCart)
                ->where('save_for_later', false)
                ->delete();

            default:
                echo 'Received unknown event type ' . $event->type;
        }
        return response('', 200);
    }

    // Connect the authenticated user to Stripe Express for payments.
    public function connect()
    {
        // Check if the user has a Stripe account ID; if not, create an Express account
        if(!auth()->user()->getStripeAccountId()){
            auth()->user()->createStripeAccount(['type' => 'express']);
        }
        // If the Stripe account is not active, redirect the user to Stripe's onboarding link
        if(!auth()->user()->isStripeAccountActive()){
            return redirect(auth()->user()->getStripeAccountLink());
        }
         // If the account is already connected, return back with a success message
        return back()->with('success', 'Your account is already connected.');
    }
}
