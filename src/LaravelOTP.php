<?php

namespace MKD\LaravelOTP;

class LaravelOTP
{
    private string $secret;

    /**
     * @param string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param string $secret
     * @return LaravelOTP
     * init the service
     */
    public static function make(string $secret): LaravelOTP
    {
        return new static($secret);
    }

    /**
     * @return string
     * Return TOTP at current timeframe
     */
    final public function now(): string
    {
        return $this->at();
    }

    /**
     * @return string
     * return TOTP at previous timeframe
     */
    final public function last(): string
    {
        return $this->at(-1);
    }

    /**
     * @return string
     * return TOTP at next timeframe
     */
    final public function next(): string
    {
        return $this->at(1);
    }

    /**
     * @param int $offset
     * @return string
     * return TOTP at custom timeframe
     */
    final public function at(int $offset = 0): string
    {
        return $this->generateTOTP($this->secret, $offset);
    }

    /**
     * @param int $length
     * @return string
     * Generate Secret Key
     */
    final public function generateSecretKey(): string
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'), 0, 32);
    }

    /**
     * @return int
     * Return current time
     */
    private function getCurrentTimestamp(): int
    {
        return floor(time() / 30); // Time steps of 30 seconds
    }

    /**
     * @param int $counter
     * @return string
     * Return HOTP at specific counter
     */
    final public function atCounter(int $counter): string
    {
        return $this->generateHOTP($this->secret, $counter);
    }

    /**
     * @param string $secretKey
     * @param int $timeStepOffset
     * @return string
     * Generate TOTP at specific timeframe
     */
    private function generateTOTP(string $secretKey, int $timeStepOffset = 0): string
    {
        $timestamp = $this->getCurrentTimestamp() + $timeStepOffset;

        return $this->generateHOTP($secretKey, $timestamp); // TOTP is HOTP with a time-based counter
    }

    /**
     * @param string $secretKey
     * @param int $counter
     * @return string
     * Generate HOTP at specific counter
     */
    private function generateHOTP(string $secretKey, int $counter): string
    {
        // Decode the base32 secret key
        $key = $this->base32_decode($secretKey);

        // Pack the counter into an 8-byte binary string (big endian)
        $counter = pack('N*', 0) . pack('N*', $counter);

        // Generate HMAC-SHA1 hash using the secret key and packed counter
        $hash = hash_hmac('sha1', $counter, $key, true);

        // Extract dynamic binary code (truncated hash)
        $offset = ord($hash[19]) & 0xf;
        $binaryCode = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        );

        // Convert binary code into a 6-digit OTP
        return str_pad($binaryCode % 1000000, 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $input
     * @return string
     */
    private function base32_decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $buffer = 0;
        $bitsLeft = 0;

        foreach (str_split($input) as $char) {
            $buffer = ($buffer << 5) | strpos($alphabet, $char);
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xff);
            }
        }

        return $output;
    }

    /**
     * @param string $otp
     * @param string|null $secret
     * @return bool
     * Verify TOTP
     */
    final public function verifyTOTP(string $otp, string $secret = null): bool
    {
        if ($secret !== null) {
            $this->secret = $secret;
        }

        $secretKey = $this->secret;

        // Check the OTP for the current time step, the previous, and the next one
        for ($i = -1; $i <= 1; $i++) {
            $calculatedOtp = $this->generateTOTP($secretKey, $i);
            if ($calculatedOtp === $otp) {
                return true; // OTP is valid
            }
        }

        return false; // OTP is invalid
    }

    /**
     * @param string $otp
     * @param int $counter
     * @param string|null $secret
     * @return bool
     * Verify HOTP at specific counter
     */
    final public function verifyHOTP(string $otp, int $counter, string $secret = null): bool
    {
        if ($secret !== null) {
            $this->secret = $secret;
        }

        $calculatedOtp = $this->generateHOTP($this->secret, $counter);
        return $calculatedOtp === $otp; // Return true if OTP matches, otherwise false
    }


    /**
     * @param string $label
     * @param string $issuer
     * @param string|null $secretKey
     * @param int|null $counter
     * @return string
     * Generate URL to be used on QR Codes for Authenticator apps
     */
    final public function generateUrl(string $label, string $issuer, string $secretKey = null, int $counter = null): string
    {
        $method = 'totp';
        if ($counter !== null) {
            $method = 'hotp';
        }
        return "otpauth://" . $method . '/' . $label . "?secret=" . ($secretKey ?? $this->secret) . '&issuer=' . $issuer . ($counter ? '&counter=' . $counter : '');
    }
}
