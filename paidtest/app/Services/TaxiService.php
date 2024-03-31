<?php

namespace App\Services;

use App\Models\Taxi;
use App\Models\User;
use App\Models\UserTaxi;

class TaxiService
{
    public static function validateAndBuy(User $user, Taxi $taxi): bool|string|null
    {
        if ($validate = self::canBuy($user, $taxi)) {
            return $validate;
        }

        return self::buy($user, $taxi);
    }

    private static function buy(User $user, Taxi $taxi): bool
    {
        UserService::decreaseCredits($user, $taxi->price);

        $userTaxi = new UserTaxi();
        $userTaxi->user_id = $user->id;
        $userTaxi->taxi_id = $taxi->id;
        $userTaxi->price = $taxi->price;
        $userTaxi->save();

        return true;
    }

    public static function canBuy(User $user, Taxi $taxi): ?string
    {
        if ($user->credit < $taxi->price) {
            return 'Not enough credit.';
        }

        return null;
    }
}
