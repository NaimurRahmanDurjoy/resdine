<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Membership;
use App\Models\OrderMaster;
use App\Models\LoyaltyPoint;

class LoyaltyService
{
    /**
     * Apply membership discount to an order amount.
     */
    public function applyDiscount(Customer $customer, $amount)
    {
        $membership = $customer->currentMembership();
        if (!$membership) {
            return $amount;
        }

        $discount = ($amount * $membership->discount_percentage) / 100;
        return $amount - $discount;
    }

    /**
     * Accrue points for a completed order.
     */
    public function accruePoints(OrderMaster $order)
    {
        if (!$order->customer_id) {
            return;
        }

        $customer = Customer::find($order->customer_id);
        $membership = $customer->currentMembership();
        
        $multiplier = $membership ? $membership->loyalty_multiplier : 1;
        $pointsToEarn = $order->total_amount * $multiplier; // $1 = 1 point * multiplier

        $loyalty = LoyaltyPoint::firstOrCreate(
            ['customer_id' => $customer->id],
            ['points_earned' => 0, 'points_redeemed' => 0]
        );

        $loyalty->increment('points_earned', $pointsToEarn);

        $this->checkTierUpgrade($customer);
    }

    /**
     * Check and upgrade customer tier based on total earned points.
     */
    protected function checkTierUpgrade(Customer $customer)
    {
        $totalPoints = $customer->loyaltyPoints->points_earned;
        $currentTier = $customer->currentMembership();
        
        $newTier = Membership::getTierByPoints($totalPoints);

        if ($newTier && (!$currentTier || $newTier->id !== $currentTier->id)) {
            $customer->memberships()->syncWithPivotValues([$newTier->id], [
                'card_no' => $currentTier ? $currentTier->pivot->card_no : 'AUTO-' . strtoupper(uniqid()),
                'start_date' => now(),
            ]);
        }
    }
}
