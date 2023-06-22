<?php

namespace App\Service;

class TokenService
{

    public function generateToken(int $length = 32): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $characterLength = strlen($characters);
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[random_int(0, $characterLength - 1)];
        }

        return $token;
    }
}