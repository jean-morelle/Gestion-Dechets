<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TwoFactorService
{
    /**
     * Générer une clé secrète pour l'authentification 2FA
     */
    public function generateSecretKey(): string
    {
        return Str::random(32);
    }

    /**
     * Générer les codes de récupération
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(8);
        }
        return $codes;
    }

    /**
     * Générer l'URL du QR Code pour Google Authenticator
     */
    public function generateQRCodeUrl(User $user): string
    {
        $secret = $user->two_factor_secret;
        $issuer = config('app.name');
        $accountName = $user->email;

        // URL TOTP simplifiée et compatible
        $url = "otpauth://totp/{$accountName}?secret={$secret}&issuer={$issuer}";
        
        return $url;
    }

    /**
     * Vérifier le code TOTP
     */
    public function verifyCode(User $user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        $secret = $user->two_factor_secret;
        $time = floor(time() / 30);
        
        // Vérifier le code actuel et les codes précédent/suivant (tolérance de 1 période)
        for ($i = -1; $i <= 1; $i++) {
            $expectedCode = $this->generateTOTP($secret, $time + $i);
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Générer un code TOTP
     */
    private function generateTOTP(string $secret, int $time): string
    {
        $key = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $time);
        $hm = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hm[19]) & 0xf;
        $code = (
            ((ord($hm[$offset+0]) & 0x7f) << 24) |
            ((ord($hm[$offset+1]) & 0xff) << 16) |
            ((ord($hm[$offset+2]) & 0xff) << 8) |
            (ord($hm[$offset+3]) & 0xff)
        ) % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Décoder une chaîne Base32
     */
    private function base32Decode(string $data): string
    {
        $map = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '2', '3', '4', '5', '6', '7', '='
        );
        
        $data = strtoupper($data);
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0, $j = strlen($data); $i < $j; $i++) {
            $v <<= 5;
            if ($data[$i] !== '=') {
                $v += array_search($data[$i], $map);
            }
            $vbits += 5;
            if ($vbits >= 8) {
                $vbits -= 8;
                $output .= chr($v >> $vbits);
            }
        }
        
        return $output;
    }

    /**
     * Encoder une chaîne en Base32
     */
    public function base32Encode(string $data): string
    {
        $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0, $j = strlen($data); $i < $j; $i++) {
            $v = ($v << 8) | ord($data[$i]);
            $vbits += 8;
            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $map[$v >> $vbits];
                $v &= (1 << $vbits) - 1;
            }
        }
        
        if ($vbits > 0) {
            $output .= $map[$v << (5 - $vbits)];
        }
        
        return $output;
    }

    /**
     * Vérifier un code de récupération
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        
        if (in_array($code, $recoveryCodes)) {
            // Supprimer le code utilisé
            $recoveryCodes = array_filter($recoveryCodes, function($c) use ($code) {
                return $c !== $code;
            });
            
            $user->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
            $user->save();
            
            return true;
        }
        
        return false;
    }
}



namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TwoFactorService
{
    /**
     * Générer une clé secrète pour l'authentification 2FA
     */
    public function generateSecretKey(): string
    {
        return Str::random(32);
    }

    /**
     * Générer les codes de récupération
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(8);
        }
        return $codes;
    }

    /**
     * Générer l'URL du QR Code pour Google Authenticator
     */
    public function generateQRCodeUrl(User $user): string
    {
        $secret = $user->two_factor_secret;
        $issuer = config('app.name');
        $accountName = $user->email;

        // URL TOTP simplifiée et compatible
        $url = "otpauth://totp/{$accountName}?secret={$secret}&issuer={$issuer}";
        
        return $url;
    }

    /**
     * Vérifier le code TOTP
     */
    public function verifyCode(User $user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        $secret = $user->two_factor_secret;
        $time = floor(time() / 30);
        
        // Vérifier le code actuel et les codes précédent/suivant (tolérance de 1 période)
        for ($i = -1; $i <= 1; $i++) {
            $expectedCode = $this->generateTOTP($secret, $time + $i);
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Générer un code TOTP
     */
    private function generateTOTP(string $secret, int $time): string
    {
        $key = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $time);
        $hm = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hm[19]) & 0xf;
        $code = (
            ((ord($hm[$offset+0]) & 0x7f) << 24) |
            ((ord($hm[$offset+1]) & 0xff) << 16) |
            ((ord($hm[$offset+2]) & 0xff) << 8) |
            (ord($hm[$offset+3]) & 0xff)
        ) % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Décoder une chaîne Base32
     */
    private function base32Decode(string $data): string
    {
        $map = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '2', '3', '4', '5', '6', '7', '='
        );
        
        $data = strtoupper($data);
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0, $j = strlen($data); $i < $j; $i++) {
            $v <<= 5;
            if ($data[$i] !== '=') {
                $v += array_search($data[$i], $map);
            }
            $vbits += 5;
            if ($vbits >= 8) {
                $vbits -= 8;
                $output .= chr($v >> $vbits);
            }
        }
        
        return $output;
    }

    /**
     * Encoder une chaîne en Base32
     */
    public function base32Encode(string $data): string
    {
        $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0, $j = strlen($data); $i < $j; $i++) {
            $v = ($v << 8) | ord($data[$i]);
            $vbits += 8;
            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $map[$v >> $vbits];
                $v &= (1 << $vbits) - 1;
            }
        }
        
        if ($vbits > 0) {
            $output .= $map[$v << (5 - $vbits)];
        }
        
        return $output;
    }

    /**
     * Vérifier un code de récupération
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        
        if (in_array($code, $recoveryCodes)) {
            // Supprimer le code utilisé
            $recoveryCodes = array_filter($recoveryCodes, function($c) use ($code) {
                return $c !== $code;
            });
            
            $user->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
            $user->save();
            
            return true;
        }
        
        return false;
    }
}








































































