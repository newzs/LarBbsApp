<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    private $thread;

    public function test_a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Models\Channel');
        $threadInChannel = create('App\Models\Thread',['channel_id' => $channel->id]);
        $threadNotInChanne = create('App\Models\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChanne->title);
    }

    public function test_a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\Models\User',['name' => 'NoNo1']));

        $threadByNoNo1 = create('App\Models\Thread',['user_id' => auth()->id()]);
        $threadNotByNoNo1 = create('App\Models\Thread');

        $this->get('threads?by=NoNo1')
            ->assertSee($threadByNoNo1->title)
            ->assertDontSee($threadNotByNoNo1->title);
    }

    public function test_a_user_can_filter_threads_by_popularity()
    {
        // Given we have three threads
        // With 2 replies,3 replies,0 replies, respectively
        $threadWithTwoReplies = create('App\Models\Thread');

        create('App\Models\Reply',['thread_id'=>$threadWithTwoReplies->id],2);

        $threadWithThreeReplies = create('App\Models\Thread');

        create('App\Models\Reply',['thread_id'=>$threadWithThreeReplies->id],3);

        $threadWithNoReplies = create('App\Models\Thread');

        // When I filter all threads by popularity
        $response = $this->getJson('threads?popularity=1')->json();

        // Then they should be returned from most replies to least.
        $this->assertEquals([3,2,0],array_column($response['data'],'replies_count'));
    }

    public function test_a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = create('App\Models\Thread');
        //create('App\Models\Reply',['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1,$response['data']);
    }

    public function test_a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create('App\Models\Thread');

        //create('App\Models\Reply',['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1,$response['data']);
    }

    public function test_we_record_a_new_visit_each_time_the_thread_is_read()
    {
        $thread = create('App\Models\Thread');

        $this->assertSame(0,$thread->visits);

        $this->call('GET',$thread->path());

        $this->assertEquals(1,$thread->fresh()->visits);
    }
}
