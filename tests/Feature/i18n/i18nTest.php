<?php

namespace Tests\Feature\i18n;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\WithUser;
use Tests\TestCase;

class i18nTest extends WithUser
{

    public function testOverview()
    {
        $this
            ->get('polyglot/api/i18n')
            ->dump()
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->has('locales', 1)
                    ->where('locales.0', 'en')
                    ->etc();
            });
    }
}