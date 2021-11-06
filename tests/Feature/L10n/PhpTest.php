<?php

namespace Tests\Feature\L10n;

use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\WithUser;

class PhpTest extends WithUser
{
    public function testGetWrongFormat()
    {
        $this
            ->get('polyglot/api/L10n/en/plain.txt')
            ->assertStatus(404);
    }
    public function testGetNotFound()
    {
        $this
            ->get('polyglot/api/L10n/en/fake.php')
            ->assertStatus(404);
    }

    public function testGetWrongLocale()
    {
        $this
            ->get('polyglot/api/L10n/ze/plain.php')
            ->assertStatus(404);
    }

    public function testGetPlain()
    {
        $this
            ->get('polyglot/api/L10n/en/plain.php')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('failed')
                    ->has('password')
                    ->has('throttle')
                    ->etc();
            });
    }

    public function testGetNested()
    {
        $this
            ->get('polyglot/api/L10n/en/nested.php')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('boolean')
                    ->has('between.file')
                    ->etc();
            });
    }

    public function testPostPlain()
    {
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en/plain.php', [
                'key' => 'test',
                'value' => $value
            ])
            ->assertStatus(200);

        $this
            ->get('polyglot/api/L10n/en/plain.php')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($value) {
                $json
                    ->where('test', $value)
                    ->etc();
            });
    }

    public function testPostNested()
    {
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en/nested.php', [
                'key' => 'between.test',
                'value' => $value
            ])
            ->assertStatus(200);

        $this
            ->get('polyglot/api/L10n/en/nested.php')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($value) {
                $json
                    ->where('between.test', $value)
                    ->etc();
            });
    }

    public function testPostDropValue()
    {
        $this
            ->post('polyglot/api/L10n/en/plain.php', [
                'key' => 'test',
                'value' => null
            ])
            ->assertStatus(200);

        $this
            ->get('polyglot/api/L10n/en/plain.php')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->where('test', '')
                    ->etc();
            });
    }
}