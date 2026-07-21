<?php

namespace App\Libraries;

class CryptoService
{
    private $key;
    private $binaryKey;
    public function __construct()
    {
        // Fetch the key from your .env file
        $hexKey = env('encryption.key', '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');
        
        // 2. Convert it to the raw 32-byte binary format ONCE during initialization
        $this->binaryKey = hex2bin($hexKey);
    }

    public function encrypt($plaintext)
    {
        $iv = openssl_random_pseudo_bytes(12);
        $tag = "";
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $this->binaryKey, OPENSSL_RAW_DATA, $iv, $tag);
        
        return [
            'result' => base64_encode($ciphertext),
            'iv'         => base64_encode($iv),
            'tag'        => base64_encode($tag)
        ];
    }

    public function decrypt($ciphertextB64, $ivB64, $tagB64)
    {
        $ciphertext = base64_decode($ciphertextB64);
        $iv         = base64_decode($ivB64);
        $tag        = base64_decode($tagB64);

        return openssl_decrypt($ciphertext, 'aes-256-gcm', $this->binaryKey, OPENSSL_RAW_DATA, $iv, $tag);
    }
}