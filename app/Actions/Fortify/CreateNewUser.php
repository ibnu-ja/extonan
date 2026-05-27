<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Junges\InviteCodes\Models\Invite;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'invite_code' => ['required', 'string',
                function ($attr, $value, $fail) {
                    $invite = Invite::where('code', $value)->first();
                    if (! $invite?->canBeRedeemed()) {
                        $fail('invite_code_expired');
                    }
                },
            ],
        ])->validate();

        $invite = Invite::where('code', $input['invite_code'])->first();
        $invite->increment('uses');

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }
}
