<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'phone','password','role','address','created_at','last_seen_at','is_active','referral_code','terms_and_condition','profile_avatar'];
    protected $returnType = 'array';


    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}