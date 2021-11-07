<?php

namespace Tests\Feature\L10n;

use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\WithUser;

class PoTest extends WithUser
{
    public function testGetWrongFormat()
    {
        $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages.txt')
            ->assertStatus(404);
    }

    public function testGetNotFound()
    {
        $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/fake')
            ->assertStatus(404);
    }

    public function testGetWrongCategory()
    {
        $this
            ->get('polyglot/api/L10n/en/LC_FAKE/messages')
            ->assertStatus(404);
    }

    public function testGetWrongLocale()
    {
        $this
            ->get('polyglot/api/L10n/ze/LC_MESSAGES/messages')
            ->assertStatus(404);
    }

    public function testGet()
    {
        $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('headers')
                    ->has('messages')
                    ->has('messages.0.msgid')
                    ->has('messages.0.msgstr')
                    ->has('messages.0.fuzzy')
                    ->has('messages.0.obsolete')
                    ->has('messages.0.context')
                    ->has('messages.0.reference')
                    ->has('messages.0.developer_comments')
                    ->has('messages.0.translator_comments')
                    ->etc();
            });
    }

    public function testPostInsertSingle()
    {
        $key = Str::random();
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => $key,
                'msgstr' => $value,
                'comment' => []
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $messages = $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('headers')
                    ->etc();
            })
            ->json('messages');

        $message = array_pop($messages);
        $this->assertEquals($key, $message['msgid']);
        $this->assertEquals($value, $message['msgstr']);
    }

    public function testPostUpdateSingle()
    {
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => 'First string.',
                'msgstr' => $value,
                'comment' => []
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $messages = $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->assertStatus(200)
            ->json('messages');

        $message = array_shift($messages);
        $this->assertEquals('First string.', $message['msgid']);
        $this->assertEquals($value, $message['msgstr']);
    }

    public function testPostInsertPlural()
    {
        $key = Str::random();
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => $key,
                'msgid_plural' => $key,
                'msgstr' => [$value, $value],
                'comment' => []
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $messages = $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('headers')
                    ->etc();
            })
            ->json('messages');

        $message = array_pop($messages);
        $this->assertEquals($key, $message['msgid']);
        $this->assertEquals($key, $message['msgid_plural']);
        $this->assertIsArray($message['msgstr']);
        $this->assertCount(2, $message['msgstr']);
        $this->assertEquals($value, $message['msgstr'][0]);
    }

    public function testPostUpdatePlural()
    {
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => '%d plural string.',
                'msgid_plural' => '%d plural strings.',
                'msgstr' => [$value, $value],
                'comment' => []
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $messages = $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('headers')
                    ->etc();
            })
            ->json('messages');

        $message = $messages[1];
        $this->assertEquals('%d plural string.', $message['msgid']);
        $this->assertEquals('%d plural strings.', $message['msgid_plural']);
        $this->assertIsArray($message['msgstr']);
        $this->assertCount(2, $message['msgstr']);
        $this->assertEquals($value, $message['msgstr'][0]);
    }

    public function testFuzzy()
    {
        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => 'First string.',
                'msgstr' => null,
                'comment' => [],
                'fuzzy' => true
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $messages = $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->assertStatus(200)
            ->json('messages');

        $message = array_shift($messages);
        $this->assertTrue($message['fuzzy']);

        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => 'First string.',
                'msgstr' => null,
                'comment' => [],
                'fuzzy' => false
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $messages = $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->assertStatus(200)
            ->json('messages');

        $message = array_shift($messages);
        $this->assertFalse($message['fuzzy']);
    }

    public function testComment()
    {
        $comment = Str::random();

        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => 'First string.',
                'msgstr' => null,
                'comment' => [$comment],
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $messages = $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->assertStatus(200)
            ->json('messages');

        $message = array_shift($messages);
        $this->assertIsArray($message['translator_comments']);
        $this->assertEquals($comment, array_pop($message['translator_comments']));
    }

    public function testPostUpdateHeaders()
    {
        $value = Str::random();

        $this
            ->post('polyglot/api/L10n/en/LC_MESSAGES/messages', [
                'msgid' => 'First string.',
                'msgstr' => $value,
                'comment' => []
            ], [
                'Accept' => 'application/json'
            ])
            ->assertStatus(200);

        $this
            ->get('polyglot/api/L10n/en/LC_MESSAGES/messages')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->etc();
            });

    }
}