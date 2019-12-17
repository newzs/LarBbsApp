<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_once_locked_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread = create('App\Models\Thread',['locked' =>true]);

        $this->post($thread->path() . '/replies',[
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }

    public function test_non_administrator_may_not_lock_threads()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Models\Thread',[
            'user_id' => auth()->id()
        ]);

        $this->post(route('locked-threads.store',$thread))->assertStatus(403);

        $this->assertFalse(!! $thread->fresh()->locked);
    }

    public function test_administrators_can_lock_threads()
    {
        $this->signIn(factory('App\Models\User')->states('administrator')->create());

        $thread = create('App\Models\Thread',['user_id' => auth()->id()]);

        // 更改
        $this->post(route('locked-threads.store',$thread));

        $this->assertTrue(!! $thread->fresh()->locked);
    }

    public function test_administrators_can_unlock_threads()
    {
        $this->signIn(factory('App\Models\User')->states('administrator')->create());

        $thread = create('App\Models\Thread',['user_id' => auth()->id(),'locked' => true]);

        $this->delete(route('locked-threads.destroy',$thread));

        $this->assertFalse($thread->fresh()->locked);
    }


}
