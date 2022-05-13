<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class AsRoles implements CastsAttributes
{

    public function get($model, string $key, $value, array $attributes)
    {
        $data = json_decode($value, true);

        return is_array($data) ?
            collect()
                ->map(function ($value) {
                    return Role::tryFrom($value);
                })
                ->filter() :
            null;
    }

    public function set($model, string $key, $value, array $attributes)
    {

        return json_encode(
            collect($value)
                ->map(function (Role $role) {
                    return $role->value;
                })
                ->toArray()
        );
    }
}
