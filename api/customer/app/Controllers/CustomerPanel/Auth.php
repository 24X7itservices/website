<?php

namespace App\Controllers\CustomerPanel;
use App\Libraries\CryptoService;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use App\Models\UserModel;

class Auth extends BaseController
{
    use ResponseTrait;
    protected $var;
    protected $crypto;
    
    public function __construct(Type $var = null) {
        $this->var = $var;
        $this->crypto = new CryptoService();
    }

    protected $helpers = ['crypto'];

    public function login()
    {

        $json = $this->request->getJSON(true);
        $credentials = decrypt_form_data($json);
        if ($credentials === false) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 401,
                'message' => 'Security verification failed. Invalid or corrupted payload.'
            ]);
        }
        
       $email    = $credentials['email'] ?? null;
       $password = $credentials['password'] ?? null;
       

      //  $decoded_data = $this->crypto->decrypt($data,$iv,$tag);

        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email);
        
        if($user){
            if (($user['role'] === "admin") && password_verify($password, $user['password'])) {
                
                $sessionData = [
                    'user_id'   => $user['id'],
                    'user_name' => $user['name'],
                    'isLoggedIn'=> true
                ];

                $secretKey   = env('JWT_SECRET');
                $currentTime = time();
                $expireTime  = $currentTime + env('JWT_TIME_TO_LIVE');

                $payload = [
                    'iss'  => base_url(),          // Issuer
                    'aud'  => base_url(),          // Audience
                    'iat'  => $currentTime,         // Issued At
                    'nbf'  => $currentTime,         // Not Before
                    'exp'  => $expireTime,          // Expiration Time
                    'data' => [
                        'userId' => 1,             // Custom identifying payload data
                        'email'  => $email,
                        'user_name' => $user['name'],
                        'isLoggedIn'=> true
                    ]
                ];
                $token = JWT::encode($payload, $secretKey, 'HS256');

                if ($user) {
                    unset($user['password']);
                }

                return $this->response
                            ->setStatusCode(200)
                            ->setJSON([
                                'message' => 'Login successful',
                                'token'    => $token,
                                'data' => $this->crypto->encrypt(json_encode($user))
                                ]);
            }
        }

        return $this->response
            ->setStatusCode(401)
            ->setJSON(['error' => 'Unauthorized']);
        
        
    }

    public function encode()
    {
            $data = $this->request->getVar('data');
            $encoded_data = $this->crypto->encrypt($data);
            return json_encode($encoded_data);
    }

    public function decode()
    {
            $data = $this->request->getVar('data');
            $iv = $this->request->getVar('iv');
            $tag = $this->request->getVar('tag');
            $decoded_data = $this->crypto->decrypt($data,$iv,$tag);
            return $decoded_data;
    }
}