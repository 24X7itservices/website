<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Extract the Authorization header
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');

        if (!$authHeader) {
            $authHeader = $request->getHeaderLine('Authorization');
        }

        // 2. Validate format: Must start with "Bearer [token]"
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return Services::response()
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['error' => 'Access Denied. Token Missing.']);
        }

        $token = $matches[1];

        try {
            // 3. Decode and verify signature & expiration
            $secretKey = env('JWT_SECRET');
            $decodedData   = JWT::decode($token, new Key($secretKey, 'HS256'));
            
            $request->decodedToken = (array) $decodedData;

        } catch (Exception $e) {
            return Services::response()
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'status' => 401,
                    'error'   => 'Invalid or Expired Token',
                    'details' => $e->getMessage()
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No execution logic required after processing
    }
}