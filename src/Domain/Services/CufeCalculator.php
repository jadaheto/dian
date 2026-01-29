<?php

namespace App\Domain\Services;

use App\Domain\Entities\Invoice;

class CufeCalculator
{
    /**
     * Calcula el CUFE según el Anexo Técnico 1.8 / 1.9
     * NumFac + FecFac + HorFac + ValFac + CodImp1 + ValImp1 + CodImp2 + ValImp2 + CodImp3 + ValImp3 + ValImp + ValPag + NitOfe + NumAdq + ClaveTec + TipoAmb
     */
    public function calculate(Invoice $invoice, string $technicalKey, string $companyNit, string $customerNit, string $environment = '2'): string
    {
        // Formatos requeridos por DIAN
        // Fechas: YYYY-MM-DD
        // Horas: HH:MM:SS-05:00 (o con Z, pero DIAN pide explícito a veces, usaremos el del objeto)

        $totals = $invoice->calculateTotals();

        // Asumimos IVA (01), INC (04), ICA (03) como los impuestos principales estándar
        // Para simplificar este MVP, sumaremos todos los impuestos al grupo 01 (IVA) si no desglosamos más
        // En un sistema real, $invoice->getIdentityTaxes() debería retornar esto desglosado.

        $valImp1 = number_format($totals['tax_amount'], 2, '.', ''); // IVA
        $codImp1 = '01';

        $valImp2 = '0.00'; // INC (Consumo)
        $codImp2 = '04';

        $valImp3 = '0.00'; // ICA
        $codImp3 = '03';

        // Construcción de la cadena de concatenación (OJO: El orden es estricto)
        $data = [
            $invoice->prefix . $invoice->number, // NumFac
            $invoice->getFormattedDate(), // FecFac
            $invoice->getFormattedTime(), // HorFac
            number_format($totals['subtotal'], 2, '.', ''), // ValFac (Valor antes de impuestos)
            $codImp1, // CodImp1
            $valImp1, // ValImp1
            $codImp2, // CodImp2
            $valImp2, // ValImp2
            $codImp3, // CodImp3
            $valImp3, // ValImp3
            number_format($totals['total'], 2, '.', ''), // ValPag (Total a Pagar)
            $companyNit, // NitOfe (Sin DV)
            $customerNit, // NumAdq (Sin DV)
            $technicalKey, // ClaveTec
            $environment // TipoAmb (1=Prod, 2=Pruebas)
        ];

        $cufeString = implode('', $data);

        // Debug (opcional, quitar en prod)
        // file_put_contents('debug_cufe_string.txt', $cufeString);

        return hash('sha384', $cufeString);
    }
}
