<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Lot;
use App\Models\Settlement;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\SettlementLifecycleService;

class BuyerPaymentController extends Controller
{
    public function show(int $settlement): View
    {
        $buyerId = $this->resolveBuyerId();
        $record = $this->findSettlementForBuyer($settlement);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $buyerId],
            ['available_balance' => 0, 'blocked_balance' => 0, 'currency' => 'USD']
        );

        $settlementTransactions = WalletTransaction::query()
            ->where('reference_type', 'settlement')
            ->where('reference_id', $record->id)
            ->latest()
            ->get();

        $walletTransactions = WalletTransaction::query()
            ->where('user_id', $buyerId)
            ->latest()
            ->take(10)
            ->get();

        return view('bid_web.buyer.payment-details', [
            'settlement' => $record,
            'wallet' => $wallet,
            'settlementTransactions' => $settlementTransactions,
            'walletTransactions' => $walletTransactions,
            'deadlineAt' => app(SettlementLifecycleService::class)->paymentDeadlineFor($record),
        ]);
    }

    public function invoice(Request $request, int $settlement)
    {
        $record = $this->findSettlementForBuyer($settlement);
        return $this->documentResponse($record, 'invoice', $request->boolean('download'));
    }

    public function receipt(Request $request, int $settlement)
    {
        $record = $this->findSettlementForBuyer($settlement);
        if ($record->status !== 'paid') {
            abort(404);
        }

        return $this->documentResponse($record, 'receipt', $request->boolean('download'));
    }

    public function payWithWallet(Request $request, int $settlement): RedirectResponse
    {
        $buyerId = $this->resolveBuyerId();

        $record = Settlement::where('id', $settlement)
            ->where('buyer_id', $buyerId)
            ->firstOrFail();

        if ($record->status === 'paid') {
            return redirect()->route('buyer.payments.show', $record->id)->with('success', 'Payment already completed.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $buyerId],
            ['available_balance' => 0, 'blocked_balance' => 0, 'currency' => 'USD']
        );

        $amount = (float) $record->amount;
        if ((float) $wallet->available_balance < $amount) {
            return redirect()->route('buyer.payments.show', $record->id)->with('error', 'Insufficient wallet balance.');
        }

        $wallet->update([
            'available_balance' => (float) $wallet->available_balance - $amount,
        ]);

        $tx = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $buyerId,
            'type' => 'auction_payment',
            'amount' => $amount,
            'status' => 'completed',
            'reference_type' => 'settlement',
            'reference_id' => $record->id,
            'payment_provider' => 'wallet',
            'payment_reference' => null,
            'description' => 'Wallet payment for settlement #' . $record->id,
        ]);

        $record->update([
            'status' => 'paid',
            'payment_provider' => 'wallet',
            'payment_reference' => 'WALLET-' . $tx->id,
            'paid_at' => now(),
        ]);

        $lot = Lot::find($record->lot_id);
        if ($lot) {
            $lot->update([
                'settlement_status' => 'paid',
            ]);
        }

        $this->notifySellerPaymentCompleted($record);

        return redirect()->route('buyer.payments.show', $record->id)->with('success', 'Payment completed using wallet.');
    }

    public function payWithBankTransfer(Request $request, int $settlement): RedirectResponse
    {
        $validated = $request->validate([
            'manual_payment_sender' => ['required', 'string', 'max:255'],
            'manual_payment_account' => ['required', 'string', 'max:255'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'manual_payment_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $buyerId = $this->resolveBuyerId();

        $record = Settlement::where('id', $settlement)
            ->where('buyer_id', $buyerId)
            ->firstOrFail();

        if ($record->status === 'paid') {
            return redirect()->route('buyer.payments.show', $record->id)->with('success', 'Payment already completed.');
        }

        $reference = $validated['payment_reference'] ?: 'BT-' . $record->id . '-' . now()->format('YmdHis');

        $record->update([
            'status' => 'processing',
            'payment_provider' => 'bank_transfer',
            'payment_reference' => $reference,
            'manual_payment_sender' => $validated['manual_payment_sender'],
            'manual_payment_account' => $validated['manual_payment_account'],
            'manual_payment_note' => $validated['manual_payment_note'] ?? null,
        ]);

        WalletTransaction::create([
            'wallet_id' => null,
            'user_id' => $buyerId,
            'type' => 'auction_payment',
            'amount' => (float) $record->amount,
            'status' => 'pending',
            'reference_type' => 'settlement',
            'reference_id' => $record->id,
            'payment_provider' => 'bank_transfer',
            'payment_reference' => $reference,
            'description' => 'Bank transfer submitted by buyer',
        ]);

        return redirect()->route('buyer.payments.show', $record->id)->with('success', 'Bank transfer submitted for verification.');
    }

    public function payWithWaafiPay(Request $request, int $settlement): RedirectResponse
    {
        $validated = $request->validate([
            'manual_payment_sender' => ['required', 'string', 'max:255'],
            'manual_payment_account' => ['required', 'string', 'max:255'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'manual_payment_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $buyerId = $this->resolveBuyerId();

        $record = Settlement::where('id', $settlement)
            ->where('buyer_id', $buyerId)
            ->firstOrFail();

        if ($record->status === 'paid') {
            return redirect()->route('buyer.payments.show', $record->id)->with('success', 'Payment already completed.');
        }

        $reference = $validated['payment_reference'] ?: 'WFP-' . $record->id . '-' . now()->format('YmdHis');

        $record->update([
            'status' => 'processing',
            'payment_provider' => 'waafipay',
            'payment_reference' => $reference,
            'manual_payment_sender' => $validated['manual_payment_sender'],
            'manual_payment_account' => $validated['manual_payment_account'],
            'manual_payment_note' => $validated['manual_payment_note'] ?? null,
        ]);

        WalletTransaction::create([
            'wallet_id' => null,
            'user_id' => $buyerId,
            'type' => 'auction_payment',
            'amount' => (float) $record->amount,
            'status' => 'pending',
            'reference_type' => 'settlement',
            'reference_id' => $record->id,
            'payment_provider' => 'waafipay',
            'payment_reference' => $reference,
            'description' => 'WaafiPay payment submitted by buyer',
        ]);

        return redirect()->route('buyer.payments.show', $record->id)->with('success', 'WaafiPay payment submitted for verification.');
    }

    public function checkoutSettlement(Request $request, int $settlement): RedirectResponse
    {
        $buyerId = $this->resolveBuyerId();

        $record = Settlement::where('id', $settlement)
            ->where('buyer_id', $buyerId)
            ->firstOrFail();

        if ($record->status === 'paid') {
            return redirect()->route('buyer.payments.show', $record->id)->with('success', 'Payment already completed.');
        }

        if (! class_exists(\Stripe\StripeClient::class)) {
            return redirect()->route('buyer.payments.show', $record->id)->with('error', 'Stripe SDK not installed.');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Auction Payment #LOT-' . str_pad((string) $record->lot_id, 4, '0', STR_PAD_LEFT),
                    ],
                    'unit_amount' => (int) round(((float) $record->amount) * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('buyer.payments.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('buyer.payments.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'settlement_id' => $record->id,
            ],
        ]);

        $record->update([
            'payment_provider' => 'stripe',
            'payment_reference' => $session->id,
        ]);

        WalletTransaction::create([
            'wallet_id' => null,
            'user_id' => $buyerId,
            'type' => 'auction_payment',
            'amount' => (float) $record->amount,
            'status' => 'pending',
            'reference_type' => 'settlement',
            'reference_id' => $record->id,
            'payment_provider' => 'stripe',
            'payment_reference' => $session->id,
            'description' => 'Stripe checkout payment',
        ]);

        return redirect($session->url);
    }

    public function checkoutSuccess(Request $request): RedirectResponse
    {
        $sessionId = (string) $request->query('session_id');
        if ($sessionId === '') {
            return redirect()->route('buyer.won-auction')->with('error', 'Invalid payment session.');
        }

        if (! class_exists(\Stripe\StripeClient::class)) {
            return redirect()->route('buyer.won-auction')->with('error', 'Stripe SDK not installed.');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $topupTx = WalletTransaction::where('payment_reference', $sessionId)
                ->where('type', 'topup')
                ->first();

            if ($topupTx) {
                $wallet = Wallet::where('id', $topupTx->wallet_id)->first();
                if ($wallet) {
                    $wallet->update([
                        'available_balance' => (float) $wallet->available_balance + (float) $topupTx->amount,
                    ]);
                }

                $topupTx->update(['status' => 'completed']);
                return redirect()->route('buyer.transactions')->with('success', 'Wallet top-up completed.');
            }

            $settlement = Settlement::where('payment_reference', $sessionId)->first();
            if (! $settlement) {
                return redirect()->route('buyer.won-auction')->with('error', 'Settlement not found.');
            }

            $wasAlreadyPaid = $settlement->status === 'paid';

            $settlement->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $lot = Lot::find($settlement->lot_id);
            if ($lot) {
                $lot->update([
                    'settlement_status' => 'paid',
                ]);
            }

            WalletTransaction::where('payment_reference', $sessionId)
                ->update(['status' => 'completed']);

            if (! $wasAlreadyPaid) {
                $this->notifySellerPaymentCompleted($settlement);
            }

            return redirect()->route('buyer.payments.show', $settlement->id)->with('success', 'Payment completed.');
        }

        return redirect()->route('buyer.won-auction')->with('error', 'Payment not completed yet.');
    }

    public function checkoutCancel(Request $request): RedirectResponse
    {
        $sessionId = (string) $request->query('session_id');
        if ($sessionId) {
            WalletTransaction::where('payment_reference', $sessionId)
                ->update(['status' => 'failed']);

            $settlement = Settlement::where('payment_reference', $sessionId)->first();
            if ($settlement) {
                $settlement->update(['status' => 'pending']);
                return redirect()->route('buyer.payments.show', $settlement->id)->with('error', 'Payment cancelled.');
            }
        }

        return redirect()->route('buyer.won-auction')->with('error', 'Payment cancelled.');
    }

    public function checkoutTopup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $buyerId = $this->resolveBuyerId();

        if (! $buyerId) {
            return back()->with('error', 'Buyer account not found.');
        }

        if (! class_exists(\Stripe\StripeClient::class)) {
            return back()->with('error', 'Stripe SDK not installed.');
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $buyerId],
            ['available_balance' => 0, 'blocked_balance' => 0, 'currency' => 'USD']
        );

        $amount = (float) $validated['amount'];

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Wallet Top-up',
                    ],
                    'unit_amount' => (int) round($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('buyer.payments.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('buyer.payments.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'wallet_topup' => '1',
                'buyer_id' => $buyerId,
            ],
        ]);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $buyerId,
            'type' => 'topup',
            'amount' => $amount,
            'status' => 'pending',
            'payment_provider' => 'stripe',
            'payment_reference' => $session->id,
            'description' => 'Stripe top-up',
        ]);

        return redirect($session->url);
    }

    private function resolveBuyerId(): ?int
    {
        $buyerId = session('logged_user.id');
        if ($buyerId) {
            return (int) $buyerId;
        }

        $fallbackId = DB::table('users')->where('type', 'buyer')->value('id');
        return $fallbackId ? (int) $fallbackId : null;
    }

    private function findSettlementForBuyer(int $settlementId): Settlement
    {
        $buyerId = $this->resolveBuyerId();

        return Settlement::with(['lot', 'seller', 'buyer'])
            ->where('id', $settlementId)
            ->where('buyer_id', $buyerId)
            ->firstOrFail();
    }

    private function documentResponse(Settlement $settlement, string $type, bool $download)
    {
        $filename = $type . '-' . $settlement->id . '.pdf';

        $pdf = Pdf::loadView('bid_web.buyer.payment-document', [
            'settlement' => $settlement,
            'lot' => $settlement->lot,
            'documentType' => $type,
            'isDownload' => $download,
        ])->setPaper('a4');

        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }

    private function notifySellerPaymentCompleted(Settlement $settlement): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        if (! $settlement->seller_id) {
            return;
        }

        $buyerName = $settlement->buyer->name
            ?? DB::table('users')->where('id', $settlement->buyer_id)->value('name')
            ?? 'Buyer';
        $lotLabel = '#LOT-' . str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT);
        $amountLabel = '$' . number_format((float) $settlement->amount, 2);

        AppNotification::create([
            'user_id' => $settlement->seller_id,
            'title' => 'Payment Received',
            'message' => "{$buyerName} completed payment of {$amountLabel} for {$lotLabel}.",
            'type' => 'success',
            'data' => [
                'lot_id' => $settlement->lot_id,
                'settlement_id' => $settlement->id,
                'url' => route('seller.sold-lots'),
            ],
        ]);
    }
}

