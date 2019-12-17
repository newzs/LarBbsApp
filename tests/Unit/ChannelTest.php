<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ChannelTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_channel_consists_of_threads(){
        $channel = create('App\Models\Channel');
        $thread = create('App\Models\Thread',['channel_id' => $channel->id]);

        $this->assertTrue($channel->threads->contains($thread));
    }
}
