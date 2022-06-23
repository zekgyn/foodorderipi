<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Menu::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // $jsonImageData = file_get_contents(base_path("/storage/app/json/menu.json"));
        // $images = json_decode(json_encode($jsonImageData), true);

        // $collectedImages =  collect($images);
        // $getCollectedImages = $collectedImages->collect();
        // $pluckedImages = $getCollectedImages->pluck('image');
        // // dd($pluckedImages);
        // return [
        //     'title' => $this->faker->words(8, true),
        //     'price' => $this->faker->randomElement([1000, 1500, 2000, 2500]),
        //     'image' => $this->faker->randomElement($pluckedImages),
        // ];
        $json = Storage::disk('local')->get('/json/menu.json');
        $menus = json_decode($json, true);
        foreach ($menus as $menu) {
            return  $menu = Menu::create([
                'title' => $this->faker->words(8, true),
                'price' => $this->faker->randomElement([1000, 1500, 2000, 2500]),
                'image' => $menu['image']
            ]);
        }
    }
}
