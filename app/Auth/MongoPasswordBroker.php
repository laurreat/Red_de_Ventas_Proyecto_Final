<?php

namespace App\Auth;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MongoPasswordBroker extends BasePasswordBroker
{
    /**
     * Create a new password reset token for the given user.
     */
    public function createToken($user)
    {
        $email = $user->getEmailForPasswordReset();
        $token = $this->generateToken();

        // Clean up old tokens
        PasswordReset::where('email', $email)->delete();

        // Create new token
        PasswordReset::create([
            'email' => $email,
            'token' => hash('sha256', $token),
            'created_at' => Carbon::now()
        ]);

        return $token;
    }

    /**
     * Generate a new password reset token.
     */
    protected function generateToken()
    {
        return Str::random(60);
    }

    /**
     * Determine if the given user recently created a password reset token.
     */
    public function recentlyCreatedToken($user)
    {
        $record = PasswordReset::where('email', $user->getEmailForPasswordReset())
                               ->where('created_at', '>', Carbon::now()->subSeconds($this->throttle))
                               ->first();

        return $record !== null;
    }

    /**
     * Delete the given password reset token.
     */
    public function deleteToken($user)
    {
        PasswordReset::where('email', $user->getEmailForPasswordReset())->delete();
    }

    /**
     * Validate the given password reset token.
     */
    public function tokenExists($user, $token)
    {
        $record = PasswordReset::where('email', $user->getEmailForPasswordReset())
                               ->where('token', hash('sha256', $token))
                               ->first();

        return $record && ! $this->tokenExpired($record);
    }

    /**
     * Determine if the token has expired.
     */
    protected function tokenExpired($record)
    {
        return Carbon::parse($record->created_at)->addSeconds($this->expire)->isPast();
    }

    /**
     * Delete expired tokens.
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expire);

        PasswordReset::where('created_at', '<', $expiredAt)->delete();
    }
}