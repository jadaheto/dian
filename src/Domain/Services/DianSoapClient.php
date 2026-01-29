<?php

namespace App\Domain\Services;

use SoapClient;
use SoapHeader;
use SoapVar;

class DianSoapClient
{
    private const WSDL_TEST = 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc?wsdl';
    private const WSDL_PROD = 'https://vpfe.dian.gov.co/WcfDianCustomerServices.svc?wsdl';

    private SoapClient $client;

    public function __construct(string $environment = 'TEST')
    {
        $wsdl = ($environment === 'PROD') ? self::WSDL_PROD : self::WSDL_TEST;

        // Opciones del cliente SOAP
        // DIAN requiere opciones específicas de contexto SSL y manejo de excepciones
        $options = [
            'soap_version' => SOAP_1_2,
            'trace' => 1,
            'exceptions' => 1,
            'connection_timeout' => 180,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT // Forzar TLS 1.2
                ]
            ])
        ];

        $this->client = new SoapClient($wsdl, $options);
    }

    /**
     * Envía un set de pruebas (Habilitación)
     */
    public function sendTestSetAsync(string $zipPath, string $testSetId): object
    {
        $zipContent = file_get_contents($zipPath);

        $params = [
            'fileName' => basename($zipPath),
            'contentFile' => $zipContent, // MTOM o base64 directo? PHP Soap suele hacer base64 auto si es string o se usa SoapVar
            'testSetId' => $testSetId
        ];

        return $this->client->SendTestSetAsync($params);
    }

    /**
     * Envía una factura electrónica (Producción / Habilitación individual)
     */
    public function sendBillAsync(string $zipPath, string $fileName): object
    {
        $zipContent = file_get_contents($zipPath);

        $params = [
            'fileName' => $fileName,
            'contentFile' => $zipContent
        ];

        return $this->client->SendBillAsync($params);
    }

    /**
     * Consulta el estado de un envío (TrackId)
     */
    public function getStatus(string $trackId): object
    {
        $params = [
            'trackId' => $trackId
        ];

        return $this->client->GetStatus($params);
    }

    /**
     * Obtener última petición (Debug)
     */
    public function getLastRequest(): string
    {
        return $this->client->__getLastRequest() ?? '';
    }

    /**
     * Obtener última respuesta (Debug)
     */
    public function getLastResponse(): string
    {
        return $this->client->__getLastResponse() ?? '';
    }
}
