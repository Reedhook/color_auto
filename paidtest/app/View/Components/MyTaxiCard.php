<?php

namespace App\View\Components;

use App\Models\Color;
use App\Models\Taxi;
use App\Models\UserTaxi;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class MyTaxiCard extends Component
{
    public function __construct(public UserTaxi $taxi)
    {

    }

    public function render(): View|Closure|string
    {
        $taxi = Taxi::find($this->taxi->taxi_id); // Получаем модель Taxi
        $colors = Color::all(); // Получаем коллекцию colors для передачи на фронт
        foreach ($colors as $value) {
            if ($taxi->color_id === $value->id) {
                $color = $value;
                break;
            }
        } // по коллекции проходимся, чтобы найти color и не делать лишних запросов в бд с find
        return view('components.my-taxi-card', ['taxi' => $this->taxi, 'color' => $color, 'colors' => $colors, 'is_free' => Auth::user()->is_color_free]);
    }
}
