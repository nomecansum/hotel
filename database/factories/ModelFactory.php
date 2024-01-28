<?php

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Planta::class, static function (Faker\Generator $faker) {
    return [
        'des_planta' => $faker->sentence,
        'id_cliente' => $faker->randomNumber(5),
        'id_edificio' => $faker->randomNumber(5),
        'id_planta' => $faker->randomNumber(5),
        
        
    ];
});
