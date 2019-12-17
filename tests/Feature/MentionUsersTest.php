<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;

    public function test_mentioned_users_in_a_reply_are_notified()
    {
        $john = create('App\Models\User',['name' => 'John']);

        $this->signIn($john);

        $jane = create('App\Models\User',['name' => 'Jane']);

        $thread = create('App\Models\Thread');

        $reply = make('App\Models\Reply',[
            'body' => '@Jane look at this. And also @Luke'
        ]);

        $this->json('post',$thread->path() . '/replies',$reply->toArray());

        $this->assertCount(1,$jane->notifications);
    }

    public function test_it_can_fetch_all_users_starting_with_the_given_characters()
    {
        create('App\Models\User',['name' => 'johndoe']);
        create('App\Models\User',['name' => 'johndoe2']);
        create('App\Models\User',['name' => 'janedoe']);

        $results = $this->json('GET','/api/users',['name' => 'john']);

        $this->assertCount(2,$results->json());
    }
}
