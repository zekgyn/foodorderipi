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
        $images = json_decode($json, true);
        foreach ($images as $image) {
            Menu::create([
                'title' => $this->faker->unique()->randomElement([
                    'mixer nyama full',
                    'mixer samaki full',
                    'wali samaki full',
                    'ugali samaki full',
                    'wali nyama full',
                    'wali mbogamboga full',
                    'ugali nyama full',
                    'ugali mbogamboga full',
                    'ndizi samaki full',
                    'ndizi samaki full',
                    'mixer kuku full',
                    'ugali kuku full',
                    'wali kuku full',
                    'ndizi kuku full',
                    'chapati samaki full',
                    'chapati kuku full',
                    'chapati nyama full',
                    'biryan nyama full',
                    'biryan kuku full'

                ]),
                'price' => $this->faker->randomElement([1000, 1500, 2000, 2500, 3000, 3500, 4000.4500]),

            ]);
        }
    }
}
