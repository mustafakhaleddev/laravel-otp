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

    final public function last(): string
    {
        return $this->at(-1);
    }

    final public function next(): string
    {
        return $this->at(1);
    }

    final public function at(int $offset = 0): string
    {
        return $this->generateTOTP($this->secret, $offset);
    }

    final public function generateSecretKey(): string
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'), 0, 32);
    }

    private function getCurrentTimestamp(): int
    {
        return floor(time() / 30);
    }

    final public function atCounter(int $counter): string
    {
        return $this->generateHOTP($this->secret, $counter);
    }

    private function generateTOTP(string $secretKey, int $timeStepOffset = 0): string
    {
        $timestamp = $this->getCurrentTimestamp() + $timeStepOffset;

        return $this->generateHOTP($secretKey, $timestamp);
    }

    private function generateHOTP(string $secretKey, int $counter): string
    {
        $key = $this->base32_decode($secretKey);
        $counter = pack('J', $counter);

        $hash = hash_hmac('sha512', $counter, $key, true);

        $offset = ord($hash[63]) & 0xf;
        $binaryCode = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        );

        return str_pad($binaryCode % 1000000, 6, '0', STR_PAD_LEFT);
    }

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

    final public function verifyTOTP(string $otp, string $secret = null): bool
    {
        if ($secret !== null) {
            $this->secret = $secret;
        }

        for ($i = -1; $i <= 1; $i++) {
            if ($this->generateTOTP($this->secret, $i) === $otp) {

                return true;
            }
        }

        return false;
    }

    final public function verifyHOTP(string $otp, int $counter, string $secret = null): bool
    {
        if ($secret !== null) {
            $this->secret = $secret;
        }

        return $this->generateHOTP($this->secret, $counter) === $otp;
    }

    final public function generateUrl(string $label, string $issuer, string $secretKey = null, int $counter = null): string
    {
        $method = $counter !== null ? 'hotp' : 'totp';
        return "otpauth://" . $method . '/' . $label . "?secret=" . ($secretKey ?? $this->secret) . '&issuer=' . $issuer . ($counter ? '&counter=' . $counter : '');
    }
}
