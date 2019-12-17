<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritiesTest extends TestCase
{
    use DatabaseMigrations;

    public function test_au_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn(); //登录

        $reply = create('App\Models\Reply');

        // If I post a "favorite" endpoint
        $this->post('replies/' . $reply->id . '/favorites');
        // It Should be recored in the database

        $this->assertCount(1,$reply->favorites);
    }

    public function test_guests_can_not_favorite_anything()
    {
        $this->withExceptionHandling()
            ->post('/replies/1/favorites')
            ->assertRedirect('/login');
    }

    public function test_au_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();

        $reply = create('App\Models\Reply');

        try{
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        }catch (\Exception $e){
            $this->fail('Did not expect to insert the same record set twice.');
        }

        $this->assertCount(1,$reply->favorites);
    }

    public function test_an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->signIn();

        $reply = create('App\Models\Reply');

        $reply->favorite();

        $this->assertCount(1,$reply->favorites);

        $this->delete('replies/' . $reply->id . '/favorites');

        $this->assertCount(0,$reply->refresh()->favorites);
    }
}
