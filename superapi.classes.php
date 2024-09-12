<?php

class SuperAPI
{
    public $baseUrl;
    public $encryptKey;
    public $operatorToken;
    public $seamlessOrSecretKey;

    public function __construct($baseUrl, $encryptKey, $operatorToken, $seamlessOrSecretKey)
    {
        // check if empty or null of all parameters then error
        if (empty($baseUrl) || empty($encryptKey) || empty($operatorToken) || empty($seamlessOrSecretKey)) {
            throw new Exception('All parameters are required');
        }

        $this->baseUrl = $baseUrl;
        $this->encryptKey = $encryptKey;
        $this->operatorToken = $operatorToken;
        $this->seamlessOrSecretKey = $seamlessOrSecretKey;
    }

    public function client($url, $method, $data = null)
    {
        $rawAuth = $this->operatorToken . ':' . $this->seamlessOrSecretKey;
        $authorizationToken = $this->encrypt($rawAuth, $this->encryptKey);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'X-Authorization-Token: ' . $authorizationToken,
        ));

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    public function encrypt($text, $key) {
        if (strlen($key) !== 32) {
            throw new Exception("Key must be 32 bytes long.");
        }
    
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    
        if ($encrypted === false) {
            throw new Exception("Encryption failed.");
        }
    
        $data = $iv . $encrypted;
        return base64_encode($data);
    }
    
    public function decrypt($data, $key) {
        if (strlen($key) !== 32) {
            throw new Exception("Key must be 32 bytes long.");
        }
    
        $data = base64_decode($data);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    
        if ($decrypted === false) {
            throw new Exception("Decryption failed.");
        }
    
        return $decrypted;
    }

    public function getGameLink(array $args) {
        $url = $this->baseUrl . '/v1/launch';
        return $this->client($url, 'POST', [
            'playerUsername' => $args['playerUsername'],
            'deviceType' => 'mobile',
            'lang' => 'en',
            'returnUrl' => $args['returnUrl'],
            'playerIp' => $args['playerIp'],
            'launchCode' => $args['launchCode'],
            'currencyCode' => 'THB'
        ]);
    }

    public function getProductList() {
        $url = $this->baseUrl . '/v1/products';
        return $this->client($url, 'GET');
    }

    public function getGameList() {
        $url = $this->baseUrl . '/v1/games';
        return $this->client($url, 'GET');
    }

    public function getAgentInfo() {
        $url = $this->baseUrl . '/v1/agent-information';
        return $this->client($url, 'GET');
    }

    public function transferGetBalance(array $args) {
        $url = $this->baseUrl . '/v1/transfer/get-balance';
        return $this->client($url, 'POST', [
            'playerUsername' => $args['playerUsername'],
            'currencyCode' => $args['currencyCode'],
        ]);
    }

    public function transferDebit(array $args) {
        $url = $this->baseUrl . '/v1/transfer/debit';
        return $this->client($url, 'POST', [
            'playerUsername' => $args['playerUsername'],
            'currencyCode' => $args['currencyCode'],
            'amount' => $args['amount'],
            'transactionId' => $args['transactionId'],
        ]);
    }

    public function transferCredit(array $args) {
        $url = $this->baseUrl . '/v1/transfer/credit';
        return $this->client($url, 'POST', [
            'playerUsername' => $args['playerUsername'],
            'currencyCode' => $args['currencyCode'],
            'amount' => $args['amount'],
            'transactionId' => $args['transactionId'],
        ]);
    }
}