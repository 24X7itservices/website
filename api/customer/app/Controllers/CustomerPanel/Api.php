<?php

namespace App\Controllers\CustomerPanel;
use App\Libraries\CryptoService;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use App\Models\ContactformModel;
use App\Models\JobsModel;
use App\Models\QuotationrequestModel;

class Api extends BaseController
{
    use ResponseTrait;
    protected $var;    // <-- ADD THIS
    protected $crypto;
    protected $helpers = ['crypto'];

    public function __construct(Type $var = null) {
        $this->var = $var;
        $this->crypto = new CryptoService();
    }

    public function getProfile()
    {       
        $userModel = new UserModel();

        $data = $this->request->getVar('userData');
        $iv = $this->request->getVar('iv');
        $tag = $this->request->getVar('tag');
        $decoded_data = $this->crypto->decrypt($data,$iv,$tag);

        $json = $this->request->getJSON(true);
        $decryptedRawText = $this->crypto->decrypt(
            $json['userData'] ?? '',
            $json['iv'] ?? '',
            $json['tag'] ?? ''
        );
        if ($decryptedRawText === false) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Decryption or integrity check failed']);
        }
        $userid = json_decode($decryptedRawText, true);

        $userData = $userModel->find(json_decode($userid));
        if ($userData) {
            unset($userData['password']); // Removes the password key from the array
        }

        if(!$userData){
        return $this->response->setJSON([
            'status' => '404',
            'message'=> "User Not Found",
            'data'   => $this->crypto->encrypt(json_encode($userData))
        ]);
        }
        
        return $this->respond([
            'status'   => 200,
            'message' => 'User Successfully Fetched',
            'data' => $this->crypto->encrypt(json_encode($userData))
        ]);
    }

    public function contactformsubmit(){

        $contactformModel = new ContactformModel();

        $json = $this->request->getJSON(true);
        $credentials = decrypt_form_data($json);
        if ($credentials === false) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 401,
                'message' => 'Security verification failed. Invalid or corrupted payload.'
            ]);
        }
        
       $name = $credentials['name'] ?? null;
       $email = $credentials['email'] ?? null;
       $subject = $credentials['subject'] ?? null;
       $message = $credentials['message'] ?? null;

       $formdatainsert= [
        'subject' => $subject, 
        'message' => $message, 
        'status' => "pending",
        'name' => $name,
        'email' => $email
       ];

       if ($contactformModel->insert($formdatainsert)) {
            return $this->response->setStatusCode(200)->setJSON([
                'message' => 'Form Submitted Successfully'
            ]);
        }

        // Fallback error if database writing fails
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to save submission. Please try again later.'
        ])->setStatusCode(500);
       
    }

    public function addCustomer(){

        $userModel = new UserModel();

        $json = $this->request->getJSON(true);
        $credentials = decrypt_form_data($json);
        if ($credentials === false) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 401,
                'message' => 'Security verification failed. Invalid or corrupted payload.'
            ]);
        }
        
       $fullName = $credentials['fullName'] ?? null;
       $email = $credentials['email'] ?? null;
       $password = $credentials['password'] ?? null;
       $contact = $credentials['contact'] ?? null;
       $acceptTerms = $credentials['acceptTerms'] ?? null;
       $referralCode = $credentials['referralCode'] ?? null;

       $formdatainsert= [
        'name' => $fullName, 
        'email' => $email, 
        'phone' => $contact ,
        'password' => $password,
        'role' => "customer",
        'address' => "",
        'referral_code' => $referralCode,
        'terms_and_condition' => $acceptTerms
       ];

       if ($userModel->insert($formdatainsert)) {
            return $this->response->setStatusCode(200)->setJSON([
                'message' => 'Customer Added Successfully'
            ]);
        }

        // Fallback error if database writing fails
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to add Customer. Please try again later.'
        ])->setStatusCode(500);
    }

    public function getJobopening(){

        $careerModel = new JobsModel();


        $open_job_data = $careerModel->getByStatus('Open');
        $job_count = count($open_job_data);

        if($job_count == 0){
            return $this->response->setJSON([
                'status' => '204',
                'data'   => ''
            ])->setStatusCode(204);
        }

        return $this->response->setJSON([
            'status' => '200',
            'data'   => $this->crypto->encrypt(json_encode($open_job_data))
        ])->setStatusCode(200);

    }

    public function quotationRequestsubmit(){

        $quotationForm = new QuotationrequestModel();

        $json = $this->request->getJSON(true);
        $credentials = decrypt_form_data($json);
        if ($credentials === false) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 401,
                'message' => 'Security verification failed. Invalid or corrupted payload.'
            ]);
        }
        
       $full_name = $credentials['fullname'] ?? null;
       $email = $credentials['email'] ?? null;
       $phone = $credentials['phoneNumber'] ?? null;
       $service_type = $credentials['service'] ?? null;
       $installation_address = $credentials['address'] ?? null;
       $description = $credentials['requirement'];

       $formdatainsert= [ 
        'full_name' => $full_name, 
        'email' => $email,
        'phone' => $phone,
        'service_type' => $service_type,
        'installation_address' => $installation_address,
        'description' => $description
       ];

       if ($quotationForm->insert($formdatainsert)) {
            return $this->response->setStatusCode(200)->setJSON([
                'message' => 'Form Submitted Successfully'
            ]);
        }

        // Fallback error if database writing fails
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Failed to save submission. Please try again later.'
        ])->setStatusCode(500);
       
    }
}