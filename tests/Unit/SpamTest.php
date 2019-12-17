<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /** @test */
    public function test_it_validates_spam()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here.'));

        $this->expectException('Exception');

        $spam->detect('something forbidden');
    }

    public function test_it_checks_for_any_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');

        $spam->detect('Hello word aaaaaaaaaa');
    }
}
