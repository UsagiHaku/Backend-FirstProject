<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

class HelloWorldTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_when_client_send_a_request_a_hello_world_is_responded()
    {
        
        # When
        # we send a GET request to the url /greeting
        $response = $this->get('api/greeting');

        # Then
        # We receive a HTTP status code of 200 (OK)
        $response->assertStatus(200);
        # We receive the texts "Hello World!" inside the response
        $response->assertSeeText('Hello World!');
    }
}

