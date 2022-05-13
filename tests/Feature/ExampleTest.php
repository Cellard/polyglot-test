<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testDotSeparatedKeys()
    {
        dump(Lang::parseKey('namespace::group.key.dot.separated'));
        dump(Lang::parseKey('group.key.dot.separated'));
        dump(Lang::parseKey('group.key.dot.separated.'));
        dump(Lang::parseKey('group.key.dot.separated'));
        dump(Lang::parseKey('Just a string. And dot'));
    }
}
