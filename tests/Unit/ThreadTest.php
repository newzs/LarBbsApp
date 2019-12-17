<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Support\Facades\Notification;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic unit test example.
     *
     * @return void
     */

    protected $thread;

    public function setUp():void
    {
        parent::setUp();
        $this->thread = create('App\Models\Thread');
    }


    public function test_a_thread_has_replies()
    {
        $thread = factory('App\Models\Thread')->create();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection',$thread->replies);
    }

    function test_a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Models\Thread');

        $this->assertInstanceOf('App\Models\Channel',$thread->channel);
    }

    function test_a_thread_can_make_a_string_path()
    {
        $thread = create('App\Models\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}",$thread->path());
    }

    public function test_a_thread_can_be_subscribed_to()
    {
        // Given we have a thread
        $thread = create('App\Models\Thread');
        // And an authenticated user
        $this->signIn();
        // When the user subscribes to the thread
        $thread->subscribe($userId = 1);
        // Then we should be able to fetch all threads that the user has subscribed to.
        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id',$userId)->count()
        );
    }

    public function test_a_thread_can_be_unsubscribed_from()
    {
        // Given we have a thread
        $thread = create('App\Models\Thread');

        // And a user who is subscribed to the thread
        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0,$thread->subscriptions);
    }

    public function test_it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        // Given we have a thread
        $thread = create('App\Models\Thread');

        // And a user who is subscribed to the thread
        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    public function test_a_thread_can_add_a_reply()
    {

        $thread = create('App\Models\Thread');

        $thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1,$thread->replies);
    }

    /** @test */
    public function test_a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();


        $this->signIn();

        $thread = create('App\Models\Thread');

        $thread
            ->subscribe()
            ->addReply([
                'body' => 'Foobar',
                'user_id' => 999 // 伪造一个与当前登录用户不同的 id
            ]);

        Notification::assertSentTo(auth()->user(),ThreadWasUpdated::class);
    }

    public function test_a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = create('App\Models\Thread');

        tap(auth()->user(),function ($user) use ($thread){
            // 对标题进行加粗显示
            $this->assertTrue($thread->hasUpdatesFor($user));
            // 浏览话题
            $user->read($thread);
            // 取消加粗
            $this->assertFalse($thread->hasUpdatesFor($user));
        });
    }

    public function test_thread_has_a_path() // 修改测试命名，更具可读性
    {
        $thread = create('App\Models\Thread');

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}",$thread->path());
    }

    public function test_a_thread_can_be_locked()
    {
        $thread=create('App\Models\Thread');

        $this->assertFalse($thread->locked);

        $thread->lock();

        $this->assertTrue($thread->locked);
    }

    public function test_a_thread_body_is_sanitized_automatically()
    {
        $thread = create('App\Models\Thread',['body' => "<script>alert('bad')</script><p>This is OK.</p>"]);

        $this->assertEquals("<p>This is OK.</p>",$thread->body);
    }

    public function test_a_reply_body_is_sanitized_automatically()
    {
        $reply = create('App\Models\Reply',['body' => "<script>alert('bad')</script><p>This is OK.</p>"]);

        $this->assertEquals("<p>This is OK.</p>",$reply->body);
    }
}
