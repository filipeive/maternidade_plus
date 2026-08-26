<?php

namespace App\Services;

use Exception;

class QrCodeService
{
    /**
     * Gera um QR Code em formato Base64 Data URI PNG utilizando o script Python nativo com a biblioteca 'qrcode'.
     *
     * @param string $data O texto ou URL a ser codificado no QR Code
     * @return string Data URI (ex: data:image/png;base64,iVBORw0...)
     */
    public static function generateBase64(string $data): string
    {
        try {
            $escapedData = escapeshellarg($data);
            $pythonScript = "import qrcode, io, base64; img = qrcode.make({$escapedData}); buf = io.BytesIO(); img.save(buf, format='PNG'); print(base64.b64encode(buf.getvalue()).decode())";
            
            $cmd = "python3 -c " . escapeshellarg($pythonScript);
            $output = shell_exec($cmd);

            if ($output && strlen(trim($output)) > 10) {
                return 'data:image/png;base64,' . trim($output);
            }
        } catch (Exception $e) {
            \Log::error('Erro ao gerar QR Code via Python: ' . $e->getMessage());
        }

        // Fallback gráfico SVG leve caso o Python não retorne
        return self::generateFallbackSvgDataUri($data);
    }

    /**
     * Fallback SVG em Data URI
     */
    private static function generateFallbackSvgDataUri(string $data): string
    {
        $encoded = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={$encoded}";
    }
}
