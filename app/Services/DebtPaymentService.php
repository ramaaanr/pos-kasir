<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Exception;

class DebtPaymentService
{
    /**
     * Process a debt payment.
     *
     * @param Debt $debt
     * @param array $data ['amount', 'payment_method', 'paid_at', 'note']
     * @return DebtPayment
     * @throws Exception
     */
    public function processPayment(Debt $debt, array $data)
    {
        return DB::transaction(function () use ($debt, $data) {
            // 1. Lock the debt record for update to prevent race conditions
            $debt = Debt::where('id', $debt->id)->lockForUpdate()->first();

            if ($debt->status === 'PAID') {
                throw new Exception("Hutang ini sudah lunas.");
            }

            $amount = (int) $data['amount'];
            if ($amount <= 0) {
                throw new Exception("Nominal pembayaran harus lebih besar dari 0.");
            }

            $remaining = $debt->remaining_balance;
            if ($amount > $remaining) {
                throw new Exception("Nominal pembayaran (Rp " . number_format($amount, 0, ',', '.') . ") melebihi sisa hutang (Rp " . number_format($remaining, 0, ',', '.') . ").");
            }

            // 2. Create Debt Payment Record
            $payment = DebtPayment::create([
                'debt_id' => $debt->id,
                'customer_id' => $debt->customer_id,
                'user_id' => auth()->id(),
                'amount' => $amount,
                'payment_method' => $data['payment_method'],
                'paid_at' => $data['paid_at'] ?? now(),
                'note' => $data['note'] ?? null,
            ]);

            // 3. Update Debt Status
            $newRemaining = $remaining - $amount;
            $debt->update([
                'status' => $newRemaining <= 0 ? 'PAID' : 'PARTIAL'
            ]);

            // 4. Update Customer Total Debt
            $customer = Customer::where('id', $debt->customer_id)->lockForUpdate()->first();
            $customer->decrement('total_debt', $amount);

            return $payment;
        });
    }
}
