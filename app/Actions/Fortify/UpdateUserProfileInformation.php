<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation {
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id), 'regex:/^[0-9a-zA-Z-_]+$/u'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'iidxid' => ['nullable', 'string', 'max:32', 'regex:/^[0-9-]+$/u'],
            'infinitasid' => ['nullable', 'string', 'max:32', 'regex:/^C-[0-9-]+$/u'],
            // 'apikey' => ['required', 'string', 'min:32', 'max:255', 'regex:/^[0-9a-zA-Z-]+$/u'],
            'scope' => ['string', 'max:255'],
        ], [
            'name.unique' => 'This name is already used.',
            'name.regex' => 'Please use only single-byte alphanumeric characters and underscores.',
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'iidxid' => $input['iidxid'],
                'infinitasid' => $input['infinitasid'],
                // 'apikey' => $input['apikey'],
                'scope' => $input['scope'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
