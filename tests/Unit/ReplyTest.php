<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Carbon\Carbon;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    function test_it_has_an_owner()
    {
        $reply = factory('App\Models\Reply')->create();

        $this->assertInstanceOf('App\Models\User',$reply->owner);
    }

    public function test_it_knows_if_it_was_just_published()
    {
        $reply = create('App\Models\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    public function test_it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = create('App\Models\Reply',[
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $this->assertEquals(['JaneDoe','JohnDoe'],$reply->mentionedUsers());
    }

    public function test_it_warps_mentioned_usernames_in_the_body_within_archor_tags()
    {
        $reply = create('App\Models\Reply',[
            'body' => 'Hello @Jane-Doe.'
        ]);

        $this->assertEquals(
            '<p>Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.</p>',
            $reply->body
        );
    }

    public function test_it_knows_if_it_is_the_best_reply()
    {
        $reply = create('App\Models\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->isBest());
    }
}
