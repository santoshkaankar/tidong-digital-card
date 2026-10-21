<?php
namespace App\Services\Qr;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
/** Offline SVG encoder. Bundled BSD-2-Clause libraries from the user's own prior Laravel project. */
final class OfflineQrSvg
{
    private static bool $loaded = false;
    private static function loadLibrary(): void
    {
        if (self::$loaded) return;
        $base = base_path('app/Support/QrLibraries');
        spl_autoload_register(static function (string $class) use ($base): void {
            $prefixes = [
                'BaconQrCode\\' => $base.'/bacon/bacon-qr-code/src/',
                'DASPRiD\\Enum\\' => $base.'/dasprid/enum/src/',
            ];
            foreach ($prefixes as $prefix => $dir) {
                if (! str_starts_with($class, $prefix)) continue;
                $file = $dir.str_replace('\\','/',substr($class,strlen($prefix))).'.php';
                if (is_file($file)) require $file;
                return;
            }
        });
        self::$loaded = true;
    }
    public static function make(string $url): string
    {
        self::loadLibrary();
        $qr = Encoder::encode($url, ErrorCorrectionLevel::M(), 'UTF-8');
        $matrix = $qr->getMatrix();
        $side = $matrix->getWidth();
        $margin = 4;
        $size = $side + $margin * 2;
        $path = '';
        for ($y=0;$y<$side;$y++) {
            for ($x=0;$x<$side;$x++) {
                if ($matrix->get($x,$y) === 1) {
                    $path .= 'M'.($x+$margin).' '.($y+$margin).'h1v1h-1z';
                }
            }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="288" height="288" viewBox="0 0 '.$size.' '.$size.'" role="img" aria-label="Table QR code"><rect width="'.$size.'" height="'.$size.'" fill="white"/><path fill="black" d="'.$path.'"/></svg>';
    }
}
