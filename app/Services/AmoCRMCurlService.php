<?php

declare(strict_types=1);

namespace App\Services;
use App\Contracts\CRMCurlServiceInterface;
use App\DataObjects\UserProfileDTO;
use App\Exceptions\AmoCRMAuthException;

class AmoCRMCurlService implements CRMCurlServiceInterface
{
    private $errors = [
        301 => 'Moved permanently.',
        400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
        401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
        403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
        404 => 'Not found.',
        500 => 'Internal server error.',
        502 => 'Bad gateway.',
        503 => 'Service unavailable.'
    ];

    public function __construct(
        public string $subdomain,
        public int $pipeline_id,
    )
    {

    }

    public function CRMClient(string $access_token, array $data): array
    {
        $method = "/api/v4/leads/complex";

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ];
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, "https://$this->subdomain.amocrm.ru".$method);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, STORAGE_PATH . '/' . 'cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEJAR, STORAGE_PATH . '/' . 'cookie.txt');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $code = (int) $code;

    
        if ($code < 200 || $code > 204) {
            throw new AmoCRMAuthException($code);
        }
    
    
        $Response = json_decode($out, true);
        return $Response['_embedded']['items'] ?? [];
    }

    public function CRMAuth(string $link, array $data) : array
    {
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $code = (int)$code;
        
        if ($code < 200 || $code > 204) {
            throw new AmoCRMAuthException($code);
        }        
        
        $content = json_decode($out, true);
        return $content;
    }

    public function sendLead(UserProfileDTO $userProfile, string $access_token ) : string
    {

        $data = [
            [
                "name" => $userProfile->name,
                "price" => $userProfile->price,
                "pipeline_id" => $this->pipeline_id,
                "_embedded" => [
                    "contacts" => [
                        [
                            "first_name" => $userProfile->name,
                            "custom_fields_values" => [
                                [
                                    "field_code" => "EMAIL",
                                    "values" => [
                                        [
                                            "enum_code" => "WORK",
                                            "value" => $userProfile->email
                                        ]
                                    ]
                                ],
                                [
                                    "field_code" => "PHONE",
                                    "values" => [
                                        [
                                            "enum_code" => "WORK",
                                            "value" => $userProfile->tel
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ]
        ];


        $body = json_encode($this->CRMClient($access_token, $data));

        return $body;

    }
}