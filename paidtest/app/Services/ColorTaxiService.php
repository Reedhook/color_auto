<?php

namespace App\Services;

use App\Models\Color;
use App\Models\Taxi;
use App\Models\User;
use App\Models\UserTaxi;
use Illuminate\Support\Facades\Log;

class ColorTaxiService
{
    /**
     * Метод для проверки денег на услугу
     * @param User $user
     * @param Color $color
     * @return string|null
     */
    public static function canBuy(User $user, Color $color): ?string
    {
        if ($user->credit < $color->price) {
            return 'Not enough credit.';
        }

        return null;
    }

    /**
     * Метод для проверки на цвет машины
     * @param Taxi $taxi
     * @param Color $color
     * @return string|null
     */
    public static function sameColor(Taxi $taxi, Color $color): ?string
    {
        if($taxi->color_id === $color->id){
            return 'Same Color';
        }
        return null;
    }

    /**
     * Входная точка для изменения цвета машины
     * @param User $user
     * @param Taxi $taxi
     * @param Color $color
     * @return bool|string
     */
    public static function validateAndColorUpdate(User $user, Taxi $taxi, Color $color): bool|string
    {
        if (!$user->is_color_free) {
            if ($validate = self::canBuy($user, $color)) {
                return $validate;
            } elseif ($validate = self::sameColor($taxi, $color)) {
                return $validate;
            }
        }
        return self::colorUpdate($user, $taxi, $color);
    }

    /**
     * Метод для изменения цвета машины
     * @param User $user
     * @param Taxi $taxi
     * @param Color $color
     * @return bool
     */
    public static function colorUpdate(User $user, Taxi $taxi, Color $color): bool
    {
        $taxi->update(['color_id' => $color->id]);
        if ($user->is_color_free) {
            $user->is_color_free = false;
            $user->save();
        } else {
            UserService::decreaseCredits($user, $color->price);
        }
        return true;
    }
}
