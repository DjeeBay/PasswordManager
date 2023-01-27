<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PassphraseService
{
    public function getPassphraseWithCorrectLength(string $value) : string
    {
        $cipherLengths = [
            'aes-128-cbc' => 16,
            'aes-256-cbc' => 32,
            'aes-128-gcm' => 16,
            'aes-256-gcm' => 32,
        ];

        $length = $cipherLengths[strtolower(config('app.cipher'))];
        $valueLength = strlen($value);
        if ($valueLength === $length) return $value;
        if ($valueLength < $length) {
            return str_pad($value, $length, "\0", STR_PAD_RIGHT);
        }

        return substr($value, 0, 32);
    }

    public function getPrivateEncrypter()
    {
        $user = Auth::user();

        return $user->passphrase_validator && Hash::check(Session::get('kpm.private_passphrase').env('KEEPASS_PASSPHRASE_VALIDATOR'), $user->passphrase_validator) ? new Encrypter($this->getPassphraseWithCorrectLength(Session::get('kpm.private_passphrase')), config('app.cipher')) : null;
    }
}
