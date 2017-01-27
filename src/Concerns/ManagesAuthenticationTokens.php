<?php
/**
 * (c) CAMC Ltd.
 * @author: Mike Smith <mike.smith@camc-ltd.co.uk>
 */

namespace Laravel\Dusk\Concerns;

use Illuminate\Support\Facades\Storage;

trait ManagesAuthenticationTokens
{
    protected static $keyPrefix = 'Dusk-authToken-';

    /**
     * @param $userId
     * @return string
     */
    public function generateAuthToken($userId)
    {
        $token = str_random(40);
        Storage::disk('local')->put(static::$keyPrefix.$userId, $token);
        return $token;
    }

    /**
     * @param $userId
     * @param $token
     * @return bool
     */
    public function verifyAuthToken($userId, $token)
    {
        if (Storage::disk('local')->get(static::$keyPrefix.$userId) === $token) {
            Storage::disk('local')->delete(static::$keyPrefix.$userId);
            return true;
        }
        return false;
    }
}
