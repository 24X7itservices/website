<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Target your exact Angular development origin
        header("Access-Control-Allow-Origin: http://localhost:64700");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        // Interrupt application lifecycle execution if the request is a preflight check
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            exit(); // Terminates script running immediately to send CORS approval back
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Unused
    }
}