<?php

namespace Tests\Feature\L10n;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\WithUser;

class IndexTest extends WithUser
{
    public function testCatalog()
    {
        $this
            ->get('polyglot/api/L10n')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->count(1)
                    ->where('0.locale', 'en')
                    ->where('0.json.filename', 'en.json')
                    ->has('0.strings', 2)
                    ->has('0.gettext.categories', 1)
                    ->where('0.gettext.categories.0.category', 'LC_MESSAGES')
                    ->has('0.gettext.categories.0.domains', 1)
                    ->where('0.gettext.categories.0.domains.0.filename', 'messages.po')
                    ->etc();
            });
    }
}