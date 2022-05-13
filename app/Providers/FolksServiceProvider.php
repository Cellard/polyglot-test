<?php

namespace App\Providers;

use App\Actions\Folks\CreateNewUser;
use App\Actions\Folks\UpdateUserProfileInformation;
use App\Models\Role;
use App\Models\User;
use Codewiser\Folks\Controls\Input;
use Codewiser\Folks\Controls\Label;
use Codewiser\Folks\Controls\Option;
use Codewiser\Folks\Controls\Options;
use Codewiser\Folks\Folks;
use Codewiser\Folks\FolksApplicationServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class FolksServiceProvider extends FolksApplicationServiceProvider
{
    /**
     * Register the Folks gate.
     *
     * This gate determines who can access Folks in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewFolks', function ($user = null) {
            return true;
        });
    }

    protected function usersBuilder(?Authenticatable $user): Builder
    {
        return \App\Models\User::query()->withTrashed();
    }

    protected function usersSchema(): array
    {
        $options = [];
        foreach (Role::cases() as $role) {
            $options[] = Option::make($role->value)->label($role->name);
        }

        return [
            Label::make('id'),
            Input::make('name')->type('text')->required(),
            Input::make('email')->type('email')->required(),
            Label::make('email_verified_at')->cast('boolean')->label('Email Verified'),
            Options::make('role')->options($options),
            Options::make('roles')->multiple()->options($options),
            Label::make('created_at'),
            Label::make('updated_at'),
            Label::make('deleted_at')->cast('boolean')->label('Deleted'),
        ];
    }
}
