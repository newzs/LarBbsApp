<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Thread;
use App\Models\Reply;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('123456'),
        'remember_token' => Str::random(10),
        'confirmed' => true,
    ];
});

$factory->state(App\Models\User::class,'unconfirmed',function () {
    return [
        'confirmed' => false
    ];
});

$factory->state(App\Models\User::class,'administrator',function () {
    return [
        'name' => '112112112'
    ];
});

$factory->define(App\Models\Thread::class,function ($faker){
    $title=$faker->sentence;
    return [
        'user_id' => function () {
            return factory('App\Models\User')->create()->id;
        },
        'channel_id' => function () {
            return factory('App\Models\Channel')->create()->id;
        },
        'title' => $title,
        'body' => $faker->paragraph,
        'visits' => 0,
        'slug' => Str::slug($title),
        'locked' => false
    ];
});

$factory->define(App\Models\Channel::class,function ($faker){
    $name = $faker->word;

    return [
        'name' => $name,
        'slug' => $name,
    ];
});


$factory->define(Reply::class,function ($faker){
    return [
        'thread_id' => function () {
            return factory('App\Models\Thread')->create()->id;
        },
        'user_id' => function () {
            return factory('App\Models\User')->create()->id;
        },
        'body' => $faker->paragraph,
    ];
});

$factory->define(Illuminate\Notifications\DatabaseNotification::class,function ($faker){
    return [
        'id' => Ramsey\Uuid\Uuid::uuid4()->toString(),
        'type' => 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => function (){
            return auth()->id() ?: factory('App\User')->create()->id;
        },
        'notifiable_type' => 'App\Models\User',
        'data' => ['foo' => 'bar']
    ];
});
