# Laravel TOTP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mkd/laravel-otp.svg?style=flat-square)](https://packagist.org/packages/mkdev/laravel-advanced-otp)
[![Total Downloads](https://img.shields.io/packagist/dt/mkd/laravel-otp.svg?style=flat-square)](https://packagist.org/packages/mkdev/laravel-advanced-otp)
![GitHub Actions](https://github.com/mustafakhaleddev/laravel-otp/actions/workflows/main.yml/badge.svg)

```markdown
# MKD Laravel OTP

A simple Laravel package for generating and verifying One-Time Passwords (OTPs) using TOTP (Time-based One-Time Password) and HOTP (HMAC-based One-Time Password) algorithms.

## Installation

You can install the package via Composer:

```bash
composer require mkd/laravel-otp
```
## Example
Scan This QR Code using Authenticator app
![Alt text](data:image/png;base64,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)

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



