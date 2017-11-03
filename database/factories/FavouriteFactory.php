<?php

use Faker\Generator as Faker;

/* @var Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Favourite::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        'favouriteable_id' => function () {
            return factory('App\Reply')->create()->id;
        },
        'favouriteable_type' => 'App\Reply',
    ];
});
