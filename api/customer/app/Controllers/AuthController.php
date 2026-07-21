<?php

namespace App\Controllers;

use App\Libraries\CryptoService;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    protected $crypto;

    public function __construct()
    {
        $this->crypto = new CryptoService();
    }

    public function testCrypto()
    {
        // 1. Setup your secret key (Must be exactly 32 bytes/characters for AES-256)
        $secretKey = "12345678901234567890123456789012"; 
        
        // 2. Setup your IV (Must be exactly 12 bytes / 24 Hex characters)
        $ivHex = "00112233445566778899aabb";

        // --- ENCRYPTION EXAMPLE ---
        // What you send TO your Angular application
        $sensitiveData = json_encode([
            'status' => 'success',
            'user_id' => 452,
            'token' => 'abc-secure-jwt-token'
        ]);

        try {
            $encryptedPayloadHex = $this->crypto->encrypt($sensitiveData, $secretKey, $ivHex);
            
            // --- DECRYPTION EXAMPLE ---
            // If Angular sends an encrypted string TO your backend
            $decryptedPayload = $this->crypto->decrypt($encryptedPayloadHex, $secretKey, $ivHex);

            return $this->respond([
                'message' => 'Crypto operational!',
                'sent_to_angular' => $encryptedPayloadHex, // Pass this directly to your Angular app
                'verified_backend_decryption' => json_decode($decryptedPayload)
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}