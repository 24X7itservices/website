<?php

namespace App\Models;

use CodeIgniter\Model;

class JobsModel extends Model
{
    protected $table = 'jobs_data';
    protected $primaryKey = 'id';
    protected $allowedFields = ['job_id', 'job_type', 'job_role_name','job_location','job_title','job_description','job_status'];
    protected $returnType = 'array';


    public function getByStatus(string $status)
    {
        return $this->select('*')
                    ->where('job_status', $status)
                    ->findAll();
    }
}