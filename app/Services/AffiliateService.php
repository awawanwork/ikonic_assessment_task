<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// use App\Services\ApiService;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method
        try {

            $affiliate = Affiliate::create([
                'user_id' => $merchant->user_id,
                'merchant_id' => $merchant->id,
                'commission_rate' => $commissionRate,
                'discount_code' => $this->apiService->createDiscountCode($merchant),
            ]);

            // Send an email notification
            Mail::to($email)->send(new AffiliateCreated($name));

            return $affiliate;

        } catch (\Exception $e) {
            throw new AffiliateCreateException('Failed to create affiliate: ' . $e->getMessage());
        }
    }
}
