<?php

namespace Spatie\Referer\Test;

class RefererTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_a_referer_that_was_already_saved()
    {
        $this->referer->put('google.com');

        $this->assertEquals('google.com', $this->referer->get());
    }

    /** @test */
    public function it_returns_an_empty_string_if_theres_no_referer()
    {
        $this->assertEquals('', $this->referer->get());
    }

    /** @test */
    public function it_can_forget_the_referer()
    {
        $this->referer->put('google.com');
        $this->referer->forget();

        $this->assertEquals('', $this->referer->get());
    }

    /** @test */
    public function it_saves_the_referer_to_the_session()
    {
        $this->referer->put('google.com');

        $this->assertEquals('google.com', $this->session->get(config('referer.key')));
    }

    /** @test */
    public function it_can_capture_the_referer_from_a_request_header()
    {
        $this->get('/', ['Referer' => 'https://google.com']);

        $this->assertEquals('google.com', $this->referer->get());
    }

    /** @test */
    public function it_can_capture_the_referer_from_an_utm_source_query_parameter()
    {
        $this->get('/?utm_source=google.com');

        $this->assertEquals('google.com', $this->referer->get());
    }

    /** @test */
    public function it_captures_the_referer_from_an_utm_source_if_theres_both_an_utm_source_and_a_header()
    {
        $this->get('/?utm_source=google.com', ['Referer' => 'https://spatie.be']);

        $this->assertEquals('google.com', $this->referer->get());
    }

    /** @test */
    public function the_referer_is_empty_when_it_cant_be_captured_from_a_request()
    {
        $this->get('/');

        $this->assertEquals('', $this->referer->get());
    }

    /** @test */
    public function the_referer_is_empty_when_the_current_domain_is_in_the_referer_header()
    {
        $this->get('/', ['Referer' => 'https://mysite.com']);

        $this->assertEquals('', $this->referer->get());
    }

    /** @test */
    public function the_referer_is_empty_when_there_is_an_invalid_url_in_the_referer_header()
    {
        $this->get('/', ['Referer' => 'https://///google.com']);

        $this->assertEquals('', $this->referer->get());
    }

    /** @test */
    public function if_a_request_has_an_empty_referer_it_wont_override_the_previous_one()
    {
        $this->referer->put('google.com');

        $this->get('/');

        $this->assertEquals('google.com', $this->referer->get());
    }
}