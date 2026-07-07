<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    private Google2FA $engine;

    public function __construct()
    {
        $this->engine = new Google2FA();
    }

    public function generateSecret(): string
    {
        return $this->engine->generateSecretKey();
    }

    /** Inline SVG markup for the QR code (no imagick dependency). */
    public function qrCodeSvg(User $user, string $secret): string
    {
        $otpauthUrl = $this->engine->getQRCodeUrl(
            config('app.name', '24 Horas'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(220, 1, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(8, 13, 26))),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($otpauthUrl);
    }

    public function verify(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, preg_replace('/\s+/', '', $code));
    }

    /** @return string[] Plaintext codes — show once, never persisted in plaintext. */
    public function generateRecoveryCodes(int $count = 8): array
    {
        return collect(range(1, $count))
            ->map(fn () => Str::upper(Str::random(4) . '-' . Str::random(4)))
            ->all();
    }

    /** @param string[] $plaintextCodes @return string[] */
    public function hashRecoveryCodes(array $plaintextCodes): array
    {
        return array_map(fn (string $code) => Hash::make($code), $plaintextCodes);
    }

    public function consumeRecoveryCode(User $user, string $code): bool
    {
        $hashedCodes = $user->two_factor_recovery_codes ?? [];

        foreach ($hashedCodes as $index => $hashedCode) {
            if (Hash::check($code, $hashedCode)) {
                unset($hashedCodes[$index]);
                $user->two_factor_recovery_codes = array_values($hashedCodes);
                $user->save();
                return true;
            }
        }

        return false;
    }
}
