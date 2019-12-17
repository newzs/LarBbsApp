<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_a_user_can_fetch_their_most_recent_reply()
    {
        $user = create('App\Models\User');

        $reply = create('App\Models\Reply',['user_id' => $user->id]);

        $this->assertEquals($reply->id,$user->lastReply->id);
    }

    public function test_a_user_can_determine_their_avatar_path()
    {
        $user = create('App\Models\User');

        $this->assertEquals('avatars/default.jpg',$user->avatar_path);

        $user->avatar_path = 'avatars/me.jpg';

        $this->assertEquals('avatars/me.jpg',$user->avatar_path);
    }
}
