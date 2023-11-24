<?php

declare(strict_types=1);

namespace App\Services;
use App\Contracts\CRMAuthServiceInterface;

class AmoCRMAuthService implements CRMAuthServiceInterface
{ 
      private string $link;
      private string $subdomain;
      private array $data;
      private string $token_file;
  
      public function __construct(
          string $subdomain,
          string $client_secret,
          string $client_id,
          string $auth_code,
          string $token_file,
          string $redirect_uri,
          private readonly AmoCRMCurlService $curlService,
      )
      {
          $this->subdomain = $subdomain;
          $this->link = "https://$subdomain.amocrm.ru/oauth2/access_token";
  
          $this->data = [
          'client_id'     => $client_id,
          'client_secret' => $client_secret,
          'grant_type'    => 'authorization_code',
          'code'          => $auth_code,
          'redirect_uri'  => $redirect_uri,
          ];
  
          $this->token_file = $token_file;
      
      }
    public function refreshAccess() : string 
    {
        $dataToken = file_get_contents(STORAGE_PATH . '/' . $this->token_file);
        $dataToken = json_decode($dataToken, true);

        unset($this->data['code']);
        $this->data['grant_type'] = 'refresh_token';
        $this->data['refresh_token'] = $dataToken['refresh_token'];

        if ($dataToken["endTokenTime"] - 60 < time()) {

            $body = $this->authRequest();

            return $body['access_token'];

        } else {           
           return $dataToken["access_token"];
        }
    }

    public function authRequest() : string
    {
        $content = $this->curlService->CRMAuth($this->link, $this->data);
        
        $arrParamsAmo = [
            "access_token"  => $content['access_token'],
            "refresh_token" => $content['refresh_token'],
            "token_type"    => $content['token_type'],
            "expires_in"    => $content['expires_in'],
            "endTokenTime"  => $content['expires_in'] + time(),
        ];
        
        $arrParamsAmo = json_encode($arrParamsAmo);
        
        $f = fopen(STORAGE_PATH . '/' . $this->token_file, 'w');
        fwrite($f, $arrParamsAmo);
        fclose($f);

        return $arrParamsAmo;

    }
}