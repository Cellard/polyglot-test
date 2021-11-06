<?php

namespace Tests\Feature\L10n;

use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\WithUser;

class JsonTest extends WithUser
{
    public function testGetWrongFormat()
    {
        $this
            ->get('polyglot/api/L10n/en.txt')
            ->assertStatus(404);
    }

    public function testGetNotFound()
    {
        $this
            ->get('polyglot/api/L10n/ze.json')
            ->assertStatus(404);
    }

    public function testGet()
    {
        $this
            ->get('polyglot/api/L10n/en.json')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('My string')
                    ->has('Next string')
                    ->etc();
            });
    }

    public function testPost()
    {
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en.json', [
                'key' => 'Test string',
                'value' => $value
            ])
            ->assertStatus(200);

        $this
            ->get('polyglot/api/L10n/en.json')
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($value) {
                $json
                    ->where('Test string', $value)
                    ->etc();
            });
    }

    public function testPostDropValue()
    {
        $this
            ->post('polyglot/api/L10n/en.json', [
                'key' => 'Test string',
                'value' => null
            ])
            ->assertStatus(200);

        $this
            ->get('polyglot/api/L10n/en.json')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->where('Test string', '')
                    ->etc();
            });
    }
}