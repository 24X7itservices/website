<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationrequestModel extends Model
{
    protected $table = 'quotation_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['customer_id', 'full_name', 'email','phone','service_type','installation_address','description'];
    protected $returnType = 'array';
}