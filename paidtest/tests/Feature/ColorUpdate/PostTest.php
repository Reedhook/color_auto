<?php

namespace Feature\ColorUpdate;

use App\Models\Color;
use App\Models\Taxi;
use App\Models\User;
use App\Services\UserService;
use Database\Seeders\TaxiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    // Используем временную бд

    private User $user;

    /**
     * Перед запуском тестов нужно сделать определенные вещи
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(); // По типу создание пользователей, так как без ползьзователя мы ничего не сможем сделать, чтобы не создавать пользователя в каждом запросе
        $this->seed(TaxiSeeder::class); // запускаем сид
        $previousTaxiId = null; // Инициализация переменной. Нужен, чтобы не случайно не покупался одна и та же машина

        // Цикл, чтобы хотя бы были 2 машины
        for ($i = 0; $i <= 2; $i++) {
            do { // если после 1 запроса вторым запросом случайно выбралась та же машина
                $taxi_id = Taxi::all()->random()->id;
            } while ($taxi_id == $previousTaxiId);

            $previousTaxiId = $taxi_id;

            $this->actingAs($this->user)->post("/buy/" . $taxi_id, ['taxi_id' => $taxi_id]); // Покупаем для пользователя рандомную машину из существующих
        }
    }

    /**
     * Проверка, что маршрут /list работает.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->user)->get('/list');
        $response->assertStatus(200);
    }

    /**
     * Проверка на удовлетворительный результат
     */
    public function test_the_colorUpdate_function_return_a_successful_response(): void
    {
        $taxi_id = $this->user->taxis()->pluck('id')->random();// выбираем рандомный id из списка купленных авто
        $response = $this->actingAs($this->user)->post("/colorUpdate/{$taxi_id}", ['taxi_id' => $taxi_id, 'color_id' => Color::where('id', '!=', 1)->inRandomOrder()->first()->id]); // Отправляем запрос на маршрут
        $response->assertRedirect(route('taxi.list')); // Проверка на перенаправление
        $response->assertSessionHas('success', 'Вы поменяли цвет'); // Проверка на существование соощения в ответе
    }

    /**
     * Проверка на ошибку с выбором того же цвета
     */
    public function test_same_color():void
    {
        $taxi_id = $this->user->taxis()->pluck('id')->random();// выбираем рандомный id из списка купленных авто
        $response = $this->actingAs($this->user)->post("/colorUpdate/{$taxi_id}", ['taxi_id' => $taxi_id, 'color_id' => 1]); // По дефолту цвет машин красный, поэтому достаточно отправить id красного
        $response->assertRedirect(route('taxi.list')); // Проверка на перенаправление
        $response->assertSessionHas('error', 'Same Color'); // Проверка на существование сообщения в ответе
    }

    /**
     * Проверка при нехватке денег на покупку услуги
     */
    public function test_not_enough_money():void
    {
        UserService::decreaseCredits($this->user, $this->user->credit); // Убираем все оставшиеся деньги у пользователя
        $taxi_id = $this->user->taxis()->pluck('id')->random();// выбираем рандомный id из списка купленных авто
        $response = $this->actingAs($this->user)->post("/colorUpdate/{$taxi_id}", ['taxi_id' => $taxi_id, 'color_id' => Color::where('id', '!=', 1)->inRandomOrder()->first()->id]);
        $response->assertRedirect(route('taxi.list')); // Проверка на перенаправление
        $response->assertSessionHas('error', 'Not enough credit.'); // Проверка на существование сообщения в ответе
    }
}
