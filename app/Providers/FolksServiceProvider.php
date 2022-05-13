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
    public function boot()
    {
        parent::boot();

        Folks::usersBuilder(function (?Authenticatable $user) {
            return \App\Models\User::query()->withTrashed();
        });

        $options = [];
        foreach (Role::cases() as $role) {
            $options[] = Option::make($role->value)->label($role->name);
        }

        Folks::usersSchema([
            Label::make('id'),
            Input::make('name')->type('text')->required(),
            Input::make('email')->type('email')->required(),
            Label::make('email_verified_at')->cast('boolean')->label('Email Verified'),
            Options::make('role')->options($options),
            Options::make('roles')->multiple()->options($options),
            Label::make('created_at'),
            Label::make('updated_at'),
            Label::make('deleted_at')->cast('boolean')->label('Deleted'),
        ]);

        Folks::createUsersUsing(CreateNewUser::class);
        Folks::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    }

    /**
     * Register the Folks gate.
     *
     * This gate determines who can access Folks in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewFolks', function ($user) {
            return true;
        });
    }

    /**
     * Return roles' collection.
     *
     * Every role should conform to \Codewiser\Folks\Contracts\RoleContract
     *
     * Role may be as Model, as Enum.
     *
     * @return Collection
     */
    protected function roles()
    {
        return collect();
    }
}
