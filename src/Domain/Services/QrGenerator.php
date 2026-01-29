<?php

namespace App\Domain\Services;

use App\Domain\Entities\Invoice;

class QrGenerator
{
    private const DIAN_URL_PROD = "https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=";
    private const DIAN_URL_TEST = "https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=";

    public function generateData(Invoice $invoice, string $companyNit, string $customerNit, string $environment = 'TEST'): string
    {
        // El contenido del QR según anexo 1.8:
        // NumFac, FecFac, HorFac, ValFac, CodImp, ValImp, ValPag, NitOfe, NumAdq, CUFE, URL
        // Todo separado por saltos de línea o pipe? No, el anexo describe campos específicos.
        // Pero lo más común es pasar simplemente la URL con parámetros en la nueva versión.
        // Sin embargo, muchos validadores esperan el string data completo.
        // Para DIAN 1.8/1.9 el QR realmente apunta a la URL de la DIAN con el CUFE.

        $baseUrl = ($environment === 'PROD') ? self::DIAN_URL_PROD : self::DIAN_URL_TEST;

        return $baseUrl . $invoice->cufe;
    }
}
