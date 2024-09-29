<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function ($faker) {
    static $password;

    return [
        'name' => 'admin',
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password' => $password ?: $password = bcrypt('Kpmg1234'),
        'opco_id' => $_ENV['APP_OPCO_ID'],
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Post::class, function( $faker) {
   return [
       'title' => $faker->realText(rand(20, 40)),
       'body' => $faker->realText(rand(100, 300)),
       'user_id' => function() {
            return \App\User::inRandomOrder()->first()->id;
       }
   ];
});
