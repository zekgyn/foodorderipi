<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait UseExternalServiceTrait{


    public function performRequest($method, $url,$headers=[], $body=[])
    {
        $client = new Client([
            'base_uri' => $this->baseUri
        ]);


        $response = $client->request($method, $url, ['headers' => $headers, 'json' => $body]);
        
        return json_decode($response->getBody()->getContents(), true);
    }

    public function performXmlRequest($method, $xmlData)
    {
        // libxml_use_internal_errors(true);

        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
                'Accept' => 'application/xml'
            ],
            'body' => $xmlData
        ];

        $client = new Client();

        //Get xml response data
        $response = $client->request($method, "{$this->baseUri}", $options);
        
        $responseData = $response->getBody()->getContents();

        return $this->convertXmlToJson($responseData);

    }

    public function convertXmlToJson($data)
    {
        //Remove br in xml data
        $removedBrData = preg_replace('/<br>|\n/', '', $data);

        //Conver xml string to object
        $xml_data = simplexml_load_string($removedBrData,'SimpleXMLElement',LIBXML_NOCDATA);

        //convert object to json data
        $json = json_encode($xml_data);

        return json_decode($json, TRUE);
    }


}
