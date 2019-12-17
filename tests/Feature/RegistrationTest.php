<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_a_confirmation_email_is_sent_upon_registration()
    {
        $this->withExceptionHandling();

        Mail::fake();

        // 用路由命名代替 url
        $this->post(route('register'),[
            'name' => 'NoNo1111',
            'email' => 'NoNo1111@example.com',
            'password' => '123456112',
            'password_confirmation' => '123456112'
        ]);

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    public function test_user_can_fully_confirm_their_email_addresses()
    {
        $this->withExceptionHandling();

        Mail::fake();

        $this->post('/register',[
            'name' => 'NoNo3434241',
            'email' => '112112112112@example.com',
            'password' => '112112112',
            'password_confirmation' => '112112112'
        ]);

        $user = User::whereName('NoNo3434241')->first();

        // 新注册用户未认证，且拥有 confirmation_token

        $this->assertFalse($user->confirmed);

        $this->assertNotNull($user->confirmation_token);

        $this->get(route('register.confirm',['token' => $user->confirmation_token]))
                 ->assertRedirect(route('threads.index'));

        // 当新注册用户点击认证链接，用户变成已认证，且跳转到话题列表页面
        tap($user->fresh(),function($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }

    public function test_confirming_an_invalid_token()
    {
        // 测试无效 Token
        $this->get(route('register.confirm'),['token' => 'invalid'])
            ->assertRedirect(route('threads.index'))
            ->assertSessionHas('flash','Unknown token.');
    }
}
