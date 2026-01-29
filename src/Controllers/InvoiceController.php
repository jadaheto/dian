<?php

namespace App\Controllers;

use App\Domain\Entities\Company;
use App\Domain\Entities\Customer;
use App\Domain\Entities\Invoice;
use App\Domain\Entities\InvoiceItem;
use App\Domain\Services\CufeCalculator;
use App\Domain\Services\DianSoapClient;
use App\Domain\Services\QrGenerator;
use App\Domain\Services\XmlGenerator;
use App\Domain\Services\XmlSigner;
use App\Utils\ZipHelper;

class InvoiceController
{
    public function create()
    {
        header('Content-Type: application/json');

        try {
            // 1. Obtener datos del Request (JSON)
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                throw new \Exception("Invalid JSON input");
            }

            // 2. Simular/Cargar Entidades (En real se cargarían de BD)
            // Hardcodeamos Company para el ejemplo
            $company = new Company(
                1,
                '900123456',
                '1',
                'Empresa Demo SAS',
                'facturacion@demo.com',
                'Calle 123 #45-67',
                '11001',
                '11',
                'O-13',
                '48',
                __DIR__ . '/../../storage/certs/certificado.p12',
                '1234',
                'soft-id-123',
                'pin-123',
                'test-set-id-123',
                'TEST'
            );

            // Crear Customer desde input
            $customer = new Customer(
                null,
                1,
                $input['customer']['id_type'],
                $input['customer']['id_number'],
                $input['customer']['dv'] ?? null,
                $input['customer']['name'],
                $input['customer']['email']
            );

            // Crear Factura
            $invoice = new Invoice(
                null,
                1,
                1,
                1,
                $input['prefix'],
                $input['number'],
                new \DateTime(),
                $input['payment_form']
            );

            // Agregar items
            foreach ($input['items'] as $itemData) {
                $invoice->addItem(new InvoiceItem(
                    null,
                    $itemData['code'],
                    $itemData['name'],
                    $itemData['quantity'],
                    $itemData['price'],
                    $itemData['tax_rate']
                ));
            }

            $totals = $invoice->calculateTotals();

            // 3. Calcular CUFE
            $cufeCalc = new CufeCalculator();
            // Clave técnica de resolución (Hardcoded example)
            $techKey = 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c';
            $invoice->cufe = $cufeCalc->calculate($invoice, $techKey, $company->nit, $customer->identificationNumber);

            // 4. Generar QR Data
            $qrGen = new QrGenerator();
            $invoice->qrData = $qrGen->generateData($invoice, $company->nit, $customer->identificationNumber);

            // 5. Generar XML
            $xmlGen = new XmlGenerator();
            $xmlContent = $xmlGen->generate($invoice, $company, $customer, '18760000001', '1', '100000'); // Res Hardcoded

            // 6. Firmar XML
            $signer = new XmlSigner();
            // $signer->loadP12($company->certificatePath, $company->certificatePassword);
            // $signedXml = $signer->sign($this->createDom($xmlContent));

            // Bypass signing if cert missing for demo
            $signedXml = $xmlContent;

            // 7. Guardar XML y Comprimir
            $fileName = "face_f{$invoice->prefix}{$invoice->number}";
            $xmlPath = __DIR__ . "/../../storage/xml/{$fileName}.xml";
            $zipPath = __DIR__ . "/../../storage/xml/{$fileName}.zip";

            file_put_contents($xmlPath, $signedXml);

            $zipper = new ZipHelper();
            $zipper->compress($xmlPath, $zipPath);

            // 8. Enviar a DIAN
            $soap = new DianSoapClient($company->environment);
            // $response = $soap->sendBillAsync($zipPath, "$fileName.zip");

            // Mock Response for Demo
            $response = [
                'status' => 'success',
                'message' => 'Factura procesada localmente (SOAP comentado para demo)',
                'cufe' => $invoice->cufe,
                'xml_url' => "/storage/xml/{$fileName}.xml"
            ];

            echo json_encode($response);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    private function createDom(string $xml): \DOMDocument
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        return $dom;
    }
}
