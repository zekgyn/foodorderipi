<?php

namespace Database\Seeders;

use App\Models\Menu;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $json = Storage::disk('local')->get('/json/menu.json');
        $json = file_get_contents(base_path("/database/seeders/json/menu.json"));
        $menus = json_decode($json, true);
        foreach ($menus as $menu) {
            $menu = Menu::create([
                'title' => $this->faker->words(8, true),
                'price' => $this->faker->randomElement([1000, 1500, 2000, 2500]),
                'image' => $menu['image']
            ]);
        }
    }
}
