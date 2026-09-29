<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function test_selected_locale_is_stored_and_applied_on_the_next_request(): void
    {
        $this->from('/')->post(route('locale.switch'), ['locale' => 'en'])
            ->assertRedirect('/');

        $this->assertSame('en', session('locale'));

        $this->get('/');

        $this->assertSame('en', app()->getLocale());
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $this->from('/')->post(route('locale.switch'), ['locale' => 'xx'])
            ->assertSessionHasErrors('locale');
    }
}
