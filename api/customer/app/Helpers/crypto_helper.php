<?php

use App\Libraries\CryptoService;

if (! function_exists('decrypt_form_data')) {
    /**
     * Decrypts a nested formData envelope structure.
     *
     * @param array|null $payload The raw incoming request JSON array
     * @return array|false Returns the decrypted array parameters, or false on failure
     */
    function decrypt_form_data(?array $payload)
    {
        if (!is_array($payload) || !isset($payload['formData'])) {
            return false;
        }

        $formData = $payload['formData'];
        $crypto   = new CryptoService();

        // 1. Run decryption pipeline
        $decryptedRawText = $crypto->decrypt(
            $formData['userData'] ?? '',
            $formData['iv'] ?? '',
            $formData['tag'] ?? ''
        );

        if ($decryptedRawText === false) {
            return false;
        }

        // 2. Parse stringified object back into a PHP associative array
        $cleanData = json_decode($decryptedRawText, true);

        // Fallback cleanup if the JSON string contains escaped literal quotes
        if (!is_array($cleanData)) {
            $cleanData = json_decode(trim($decryptedRawText, '"'), true);
        }

        return is_array($cleanData) ? $cleanData : false;
    }
}