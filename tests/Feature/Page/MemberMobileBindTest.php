<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace Tests\Feature\Page;

use Tests\TestCase;
use App\Services\Member\Models\User;

class MemberMobileBindTest extends TestCase
{
    public function test_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->visit(route('member.mobile.bind'))->seeStatusCode(200);
    }

    public function test_bind()
    {
        $user = User::factory()->create([
            'mobile' => '233334444',
        ]);
        $this->session(['sms_mobile_bind' => '123123']);
        $this->actingAs($user)->post(route('member.mobile.bind'), [
            'mobile' => '13900001111',
            'captcha' => '123123',
            'sms_captcha_key' => 'mobile_bind',
            'sms_captcha' => '123123',
        ])->seeStatusCode(302);

        $user->refresh();
        $this->assertEquals('13900001111', $user->mobile);
    }

    public function test_bind_with_cant()
    {
        $user = User::factory()->create([
            'mobile' => '12333334444',
        ]);
        $this->session(['sms_mobile_bind' => '123123']);
        $this->actingAs($user)->post(route('member.mobile.bind'), [
            'mobile' => '13900001111',
            'captcha' => '123123',
            'sms_captcha_key' => 'mobile_bind',
            'sms_captcha' => '123123',
        ])->seeInSession('warning');
    }

    public function test_bind_with_exists_mobile()
    {
        $user = User::factory()->create([
            'mobile' => '12333334444',
        ]);
        User::factory()->create([
            'mobile' => '13900001111',
        ]);
        $this->session(['sms_mobile_bind' => '123123']);
        $this->actingAs($user)->post(route('member.mobile.bind'), [
            'mobile' => '13900001111',
            'captcha' => '123123',
            'sms_captcha_key' => 'mobile_bind',
            'sms_captcha' => '123123',
        ])->seeInSession('warning');
    }
}
