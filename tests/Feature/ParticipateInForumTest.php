<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_unauthenticated_user_may_no_add_replies()
    {
        $this->withExceptionHandling()
            ->post('threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        // Given we have a authenticated user
        $this->signIn();
        // And an existing thread
        $thread = create('App\Models\Thread');

        // When the user adds a reply to the thread
        $reply = make('App\Models\Reply');
        $this->post($thread->path() .'/replies',$reply->toArray());

        // Then their reply should be visible on the page
//        $this->get($thread->path())
//            ->assertSee($reply->body);

        $this->assertDatabaseHas('replies',['body' => $reply->body]);
        $this->assertEquals(1,$thread->fresh()->replies_count);
    }

    public function test_a_reply_reqiures_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = create('App\Models\Thread');
        $reply = make('App\Models\Reply',['body' => null]);

        $this->post($thread->path() . '/replies',$reply->toArray())
            ->assertStatus(302);//422
    }

    public function test_unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Models\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    public function test_authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = create('App\Models\Reply',['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies',['id' => $reply->id]);
        $this->assertEquals(0,$reply->thread->fresh()->replies_count);
    }

    public function test_unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();

        $reply = create('App\Models\Reply');

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');

        $this->signIn()
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function test_authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = create('App\Models\Reply',['user_id' => auth()->id()]);

        $updatedReply = 'You have been changed,foo.';

        $this->patch("/replies/{$reply->id}",['body' => $updatedReply]);

        $this->assertDatabaseHas('replies',['id' => $reply->id,'body' => $updatedReply]);
    }

    public function test_replies_contain_spam_may_not_be_created()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Models\Thread');
        $reply = make('App\Models\Reply',[
            'body' => 'something forbidden'
        ]);

       // $this->expectException(\Exception::class);

        $this->post($thread->path() . '/replies',$reply->toArray())
            ->assertStatus(302);//422
    }

    public function test_users_may_only_reply_a_maximum_of_once_per_minute()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Models\Thread',['user_id' => auth()->id()]);

        $reply = make('App\Models\Reply',[
            'body' => 'My simple reply.'
        ]);

        $this->post($thread->path() . '/replies',$reply->toArray())
            ->assertStatus(201);

        $this->post($thread->path() . '/replies',$reply->toArray())
            ->assertStatus(429);
    }

    public function test_replies_that_contain_spam_may_not_be_created()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Models\Thread');

        $reply = make('App\Models\Reply',[
            'body' => 'something forbidden'
        ]);

        $this->post($thread->path() . '/replies',$reply->toArray())
            ->assertStatus(302);//422
    }
}
