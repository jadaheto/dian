<?php

namespace App\Domain\Services;

use App\Domain\Entities\AppEntities\Company;
use App\Domain\Entities\Customer;
use App\Domain\Entities\Invoice;
use DOMDocument;
use DOMElement;

class XmlGenerator
{
    private DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
    }

    public function generate(
        Invoice $invoice,
        Company $company,
        Customer $customer,
        string $resolutionNumber,
        string $startRange,
        string $endRange
    ): string {
        // Namespace Map DIAN 2.1
        $namespaces = [
            'xmlns' => "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2",
            'xmlns:cac' => "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
            'xmlns:cbc' => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
            'xmlns:ds' => "http://www.w3.org/2000/09/xmldsig#",
            'xmlns:ext' => "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2",
            'xmlns:sts' => "dian:gov:co:facturaelectronica:Structures-2-1",
            'xmlns:xades' => "http://uri.etsi.org/01903/v1.3.2#",
            'xmlns:xades141' => "http://uri.etsi.org/01903/v1.4.1#",
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xsi:schemaLocation' => "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd"
        ];

        // Root Element: Invoice
        $root = $this->dom->createElement('Invoice');
        foreach ($namespaces as $prefix => $uri) {
            $root->setAttribute($prefix, $uri);
        }
        $this->dom->appendChild($root);

        // 1. UBLExtensions (Donde va la firma y el QR)
        $exts = $this->dom->createElement('ext:UBLExtensions');
        $root->appendChild($exts);

        // Placeholder para firma (Extension 1)
        // Placeholder para información DIAN (Extension 2 - QR, Software, URL valida)
        $this->addDianExtensions($exts, $company, $invoice);

        // 2. Encabezado del documento
        $root->appendChild($this->dom->createElement('cbc:UBLVersionID', 'UBL 2.1'));
        $root->appendChild($this->dom->createElement('cbc:CustomizationID', '10')); // 10 = Estándar
        $root->appendChild($this->dom->createElement('cbc:ProfileID', 'DIAN 2.1: Factura Electrónica de Venta'));
        $root->appendChild($this->dom->createElement('cbc:ID', $invoice->prefix . $invoice->number));
        $root->appendChild($this->dom->createElement('cbc:UUID', $invoice->cufe))->setAttribute('schemeName', 'CUFE-SHA384');
        $root->appendChild($this->dom->createElement('cbc:IssueDate', $invoice->getFormattedDate()));
        $root->appendChild($this->dom->createElement('cbc:IssueTime', $invoice->getFormattedTime() . '-05:00'));
        $root->appendChild($this->dom->createElement('cbc:InvoiceTypeCode', '01')); // 01 = Factura Venta
        $root->appendChild($this->dom->createElement('cbc:Note', 'Factura generada por software propio'));
        $root->appendChild($this->dom->createElement('cbc:DocumentCurrencyCode', 'COP'));

        // 3. Emisor (AccountingSupplierParty)
        // 4. Receptor (AccountingCustomerParty)
        // 5. Medios de Pago
        // 6. Impuestos Totales
        // 7. Líneas de Factura

        return $this->dom->saveXML();
    }

    private function addDianExtensions(DOMElement $parent, Company $company, Invoice $invoice): void
    {
        // Aquí iría el nodo ext:UBLExtension para DianExtensions (ProviderID, SoftwareID, QRCode, Signature)
        // Se implementará en la siguiente iteración
        $ext = $this->dom->createElement('ext:UBLExtension');
        $parent->appendChild($ext);

        $content = $this->dom->createElement('ext:ExtensionContent');
        $ext->appendChild($content);

        // DianExtensions...
    }
}
