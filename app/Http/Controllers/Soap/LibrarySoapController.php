<?php

namespace App\Http\Controllers\Soap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LibrarySoapController extends Controller
{
    /**
     * Menampilkan kontrak WSDL layanan SOAP.
     */
    public function wsdl(): Response
    {
        $endpointUrl = url('/soap/sipus');

        $wsdl = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<definitions name="SIPUSLibraryService"
    targetNamespace="http://sipus-del.test/soap"
    xmlns:tns="http://sipus-del.test/soap"
    xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns="http://schemas.xmlsoap.org/wsdl/">

    <message name="CheckClearanceStatusRequest">
        <part name="nim_peminjam" type="xsd:string"/>
    </message>

    <message name="CheckClearanceStatusResponse">
        <part name="active_loans" type="xsd:int"/>
        <part name="is_cleared" type="xsd:boolean"/>
    </message>

    <portType name="LibrarySoapPortType">
        <operation name="CheckClearanceStatus">
            <input message="tns:CheckClearanceStatusRequest"/>
            <output message="tns:CheckClearanceStatusResponse"/>
        </operation>
    </portType>

    <binding name="LibrarySoapBinding" type="tns:LibrarySoapPortType">
        <soap:binding style="rpc"
            transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="CheckClearanceStatus">
            <soap:operation soapAction="CheckClearanceStatus"/>
            <input><soap:body use="encoded"
                namespace="http://sipus-del.test/soap"
                encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap:body use="encoded"
                namespace="http://sipus-del.test/soap"
                encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
    </binding>

    <service name="SIPUSLibraryService">
        <port name="LibrarySoapPort" binding="tns:LibrarySoapBinding">
            <soap:address location="{$endpointUrl}"/>
        </port>
    </service>
</definitions>
XML;

        return response($wsdl, 200)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    /**
     * Memproses permintaan SOAP pengecekan bebas pustaka.
     */
    public function checkClearance(Request $request): Response
    {
        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($request->getContent());

        if ($xml === false) {
            libxml_clear_errors();

            return $this->soapFault(
                'Client',
                'Format XML tidak valid.'
            );
        }

        $nimNodes = $xml->xpath(
            '//*[local-name()="nim_peminjam"]'
        );

        $nim = isset($nimNodes[0])
            ? trim((string) $nimNodes[0])
            : '';

        if ($nim === '') {
            return $this->soapFault(
                'Client',
                'Tag nim_peminjam wajib diisi.'
            );
        }

        $activeLoans = DB::table('peminjamans')
            ->where('nim', $nim)
            ->where('status', 'dipinjam')
            ->count();

        $isCleared = $activeLoans === 0
            ? 'true'
            : 'false';

        $responseXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope
    xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <CheckClearanceStatusResponse>
            <active_loans>{$activeLoans}</active_loans>
            <is_cleared>{$isCleared}</is_cleared>
        </CheckClearanceStatusResponse>
    </soap:Body>
</soap:Envelope>
XML;

        return response($responseXml, 200)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    /**
     * Mengembalikan SOAP Fault ketika terjadi kesalahan.
     */
    private function soapFault(
        string $code,
        string $message
    ): Response {
        $safeMessage = htmlspecialchars(
            $message,
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );

        $faultXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope
    xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <soap:Fault>
            <faultcode>soap:{$code}</faultcode>
            <faultstring>{$safeMessage}</faultstring>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>
XML;

        return response($faultXml, 400)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
