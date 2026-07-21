<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactformModel extends Model
{
    protected $table = 'contact_forms';
    protected $primaryKey = 'id';
    protected $allowedFields = ['subject', 'message', 'status','submitted_at','name','email'];
    protected $returnType = 'array';
}