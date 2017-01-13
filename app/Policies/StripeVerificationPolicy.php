<?php

namespace App\Policies;

use App\StripeVerificationRequest;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StripeVerificationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function edit(User $user, StripeVerificationRequest $stripeVerificationRequest)
    {
        $stripeVerificationRequests = $user->StripeVerificationRequests()->get();
        return $stripeVerificationRequests->contains($stripeVerificationRequest);
        //return $user->id === $channel->user_id;
    }
}
