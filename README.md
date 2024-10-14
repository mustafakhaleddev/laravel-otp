# Laravel OTP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mkd/laravel-otp.svg?style=flat-square)](https://packagist.org/packages/mkdev/laravel-advanced-otp)
[![Total Downloads](https://img.shields.io/packagist/dt/mkd/laravel-otp.svg?style=flat-square)](https://packagist.org/packages/mkdev/laravel-advanced-otp)
![GitHub Actions](https://github.com/mustafakhaleddev/laravel-otp/actions/workflows/phpunit.yml/badge.svg)



A simple Laravel package for generating and verifying One-Time Passwords (OTPs) using TOTP (Time-based One-Time Password) and HOTP (HMAC-based One-Time Password) algorithms.

## Installation

You can install the package via Composer:

```bash
composer require mkd/laravel-otp
```
## Example
Scan This QR Code using Authenticator app
<div style="text-align: center;">
    <img src="https://i.ibb.co/PhPg1m6/frame-1.png" alt="My Image" />
</div>

Here's a simple example demonstrating how to generate and verify an OTP:

```php
use MKD\LaravelOTP\LaravelOTP;

$secret = 'WSRNGQX4J57FL2POVHDAMBI6ZTK3CYUE'; // Base32 encoded secret
$otpService = LaravelOTP::make($secret);

// Generate current OTP
$currentOTP = $otpService->now();
echo "Current OTP: $currentOTP\n";

// Verify the OTP
$isValid = $otpService->verifyTOTP($currentOTP);
echo $isValid ? "OTP is valid!" : "OTP is invalid!";
```

## Usage

### Creating an Instance

You can create an instance of the `LaravelOTP` class by providing a secret key:

```php
use MKD\LaravelOTP\LaravelOTP;

$otpService = LaravelOTP::make('your-secret-key');
```

### Public Methods

#### `now(): string`

Returns the current TOTP for the current timeframe.

```php
$otp = $otpService->now();
```

#### `last(): string`

Returns the TOTP for the previous timeframe (30 seconds earlier).

```php
$otp = $otpService->last();
```

#### `next(): string`

Returns the TOTP for the next timeframe (30 seconds later).

```php
$otp = $otpService->next();
```

#### `at(int $offset = 0): string`

Returns the TOTP for a custom timeframe based on the provided offset. An offset of `0` returns the current TOTP, `-1` returns the last OTP, and `1` returns the next OTP.

```php
$otp = $otpService->at(-1); // Last TOTP
$otp = $otpService->at(1);  // Next TOTP
```

#### `generateSecretKey(): string`

Generates a new secret key.

```php
$secretKey = $otpService->generateSecretKey();
```

#### `atCounter(int $counter): string`

Returns the HOTP for a specific counter value.

```php
$otp = $otpService->atCounter(1); // OTP for counter 1
```

#### `verifyTOTP(string $otp, string|null $secret = null): bool`

Verifies a given TOTP against the current, previous, and next timeframes. If a secret key is provided, it overrides the current secret.

```php
$isValid = $otpService->verifyTOTP('123456'); // Validate TOTP
```

#### `verifyHOTP(string $otp, int $counter, string|null $secret = null): bool`

Verifies a given HOTP against a specific counter. If a secret key is provided, it overrides the current secret.

```php
$isValid = $otpService->verifyHOTP('123456', 1); // Validate HOTP for counter 1
```

## Google Authenticator Compatible

#### `generateUrl(string $label, string $issuer, string $secretKey = null, int $counter = null): string`

Generates an OTP Auth URL for use in generating a QR code. This URL can be used by OTP apps like Google Authenticator.

- **Parameters**:
    - `string $label`: A unique identifier for the OTP (usually the user's email or username).
    - `string $issuer`: The name of your application (used as the issuer for the OTP).
    - `string|null $secretKey`: An optional secret key. If not provided, the instance's secret key will be used.
    - `int|null $counter`: An optional counter for HOTP. If provided, the method will generate an HOTP URL instead of TOTP.

**Usage Example**:

```php
$label = 'user@example.com';
$issuer = 'YourAppName';
$otpUrl = $otpService->generateUrl($label, $issuer);
echo "OTP Auth URL: $otpUrl" // otpauth://totp/Name?secret=WSRNGQX4J57FL2POVHDAMBI6ZTK3CYUE&issuer=APP;
```


## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.



