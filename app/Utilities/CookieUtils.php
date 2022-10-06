<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Crypt;

class CookieUtils {

    public static function cookieDecrypt(string $cookie): string {
        return explode('|', Crypt::decryptString($cookie))[1];
    }

}
