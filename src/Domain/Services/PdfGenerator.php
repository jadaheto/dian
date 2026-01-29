<?php

namespace App\Domain\Services;

use App\Domain\Entities\Company;
use App\Domain\Entities\Customer;
use App\Domain\Entities\Invoice;
// use Dompdf\Dompdf; // Descomentar tras instalar: composer require dompdf/dompdf

class PdfGenerator
{
    public function generate(Invoice $invoice, Company $company, Customer $customer, string $outputPath): void
    {
        // 1. Renderizar vista HTML (Simulada aquí, idealmente usar un motor de plantillas)
        $html = $this->getHtmlContent($invoice, $company, $customer);

        // 2. Generar PDF
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($html);
        // $dompdf->setPaper('A4', 'portrait');
        // $dompdf->render();
        // file_put_contents($outputPath, $dompdf->output());

        // MOCK: Guardar HTML por ahora para verificar sin librerías
        file_put_contents($outputPath . '.html', $html);
        file_put_contents($outputPath, "%PDF-1.4... (Mock content for demo without libraries)");
    }

    private function getHtmlContent(Invoice $invoice, Company $company, Customer $customer): string
    {
        $totals = $invoice->calculateTotals();

        // Simple HTML Template
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <style>
                body {
                    font-family: sans-serif;
                }

                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }

                .box {
                    border: 1px solid #ccc;
                    padding: 10px;
                    margin-bottom: 10px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th,
                td {
                    border: 1px solid #ddd;
                    padding: 8px;
                }

                .totals {
                    text-align: right;
                }

                .qr {
                    text-align: center;
                    margin-top: 20px;
                }
            </style>
        </head>

        <body>
            <div class="header">
                <h2>FACTURA ELECTRÓNICA DE VENTA</h2>
                <h3>
                    <?php echo $company->companyName; ?>
                </h3>
                <p>NIT:
                    <?php echo $company->nit; ?>
                </p>
            </div>

            <div class="box">
                <strong>No.
                    <?php echo $invoice->prefix . $invoice->number; ?>
                </strong><br>
                Fecha:
                <?php echo $invoice->getFormattedDate(); ?> Hora:
                <?php echo $invoice->getFormattedTime(); ?><br>
                Forma de Pago:
                <?php echo $invoice->paymentForm == '1' ? 'Contado' : 'Crédito'; ?>
            </div>

            <div class="box">
                <strong>Adquirente:</strong>
                <?php echo $customer->name; ?><br>
                NIT/CC:
                <?php echo $customer->identificationNumber; ?><br>
                Email:
                <?php echo $customer->email; ?>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Cant</th>
                        <th>Descripción</th>
                        <th>Vr. Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoice->getItems() as $item): ?>
                        <tr>
                            <td>
                                <?php echo $item->quantity; ?>
                            </td>
                            <td>
                                <?php echo $item->productName; ?>
                            </td>
                            <td>$
                                <?php echo number_format($item->unitPrice, 2); ?>
                            </td>
                            <td>$
                                <?php echo number_format($item->getTotal(), 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="totals">
                <p>Subtotal: $
                    <?php echo $totals['subtotal']; ?>
                </p>
                <p>IVA: $
                    <?php echo $totals['tax_amount']; ?>
                </p>
                <h3>Total: $
                    <?php echo $totals['total']; ?>
                </h3>
            </div>

            <div class="qr">
                <p><strong>CUFE:</strong>
                    <?php echo $invoice->cufe; ?>
                </p>
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($invoice->qrData); ?>" />
                <p>Representación Gráfica de Factura Electrónica</p>
            </div>
        </body>

        </html>
        <?php
        return ob_get_clean();
    }
}
