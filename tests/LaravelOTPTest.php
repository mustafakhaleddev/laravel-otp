<?php

namespace MKD\tests;

use MKD\LaravelOTP\LaravelOTP;
use PHPUnit\Framework\TestCase;

class LaravelOTPTest extends TestCase
{
    private string $secret;
    private LaravelOTP $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        // Generate a secret key for testing
        $this->secret = 'JBSWY3DPEHPK3PXP'; // Example Base32 encoded key
        $this->otpService = LaravelOTP::make($this->secret);
    }

    /** @test */
    public function it_can_generate_current_totp()
    {
        $otp = $this->otpService->now();
        $this->assertIsString($otp);
        $this->assertEquals(6, strlen($otp));
    }

    /** @test */
    public function it_can_generate_last_totp()
    {
        $otp = $this->otpService->last();
        $this->assertIsString($otp);
        $this->assertEquals(6, strlen($otp));
    }

    /** @test */
    public function it_can_generate_next_totp()
    {
        $otp = $this->otpService->next();
        $this->assertIsString($otp);
        $this->assertEquals(6, strlen($otp));
    }

    /** @test */
    public function it_can_generate_totp_at_custom_timeframe()
    {
        $otp = $this->otpService->at(0); // Current OTP
        $this->assertEquals($this->otpService->now(), $otp);

        $lastOtp = $this->otpService->at(-1); // Previous OTP
        $nextOtp = $this->otpService->at(1); // Next OTP
        $this->assertNotEquals($lastOtp, $otp);
        $this->assertNotEquals($nextOtp, $otp);
    }

    /** @test */
    public function it_can_generate_secret_key()
    {
        $secretKey = $this->otpService->generateSecretKey();
        $this->assertIsString($secretKey);
        $this->assertEquals(32, strlen($secretKey)); // Adjust based on your implementation
    }

    /** @test */
    public function it_can_generate_hotp_at_specific_counter()
    {
        $counter = 1;
        $otp = $this->otpService->atCounter($counter);
        $this->assertIsString($otp);
        $this->assertEquals(6, strlen($otp));
    }

    /** @test */
    public function it_can_generate_url()
    {
        $url = $this->otpService->generateUrl('user@example.com', 'YourAppName');
        $this->assertStringContainsString('otpauth://', $url);
        $this->assertStringContainsString('user@example.com', $url);
        $this->assertStringContainsString('YourAppName', $url);
    }

    /** @test */
    public function it_can_verify_valid_totp()
    {
        $validOtp = $this->otpService->now();
        $this->assertTrue($this->otpService->verifyTOTP($validOtp));
    }

    /** @test */
    public function it_can_verify_invalid_totp()
    {
        $this->assertFalse($this->otpService->verifyTOTP('123456')); // Assuming '123456' is not valid
    }

    /** @test */
    public function it_can_verify_hotp()
    {
        $counter = 1;
        $hotp = $this->otpService->atCounter($counter);
        $this->assertTrue($this->otpService->verifyHOTP($hotp, $counter));
    }

    /** @test */
    public function it_can_verify_invalid_hotp()
    {
        $this->assertFalse($this->otpService->verifyHOTP('123456', 1)); // Assuming '123456' is not valid
    }
}
