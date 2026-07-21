<?php

namespace App\Controllers;

use App\Models\DynamicModel;

class ApiController extends BaseController
{
    private $dynamicModel;

    public function __construct()
    {
        $this->dynamicModel = new DynamicModel();
    }

    /**
     * 1. Dynamic VIEW Example
     */
    public function showData()
    {
        // Target the 'users' table, join it with 'profiles', filter by status
        $users = $this->dynamicModel
                      ->setTargetTable('users')
                      ->viewData(
                          ['users.status' => 'active'], // WHERE clause
                          [['table' => 'user_profiles', 'cond' => 'user_profiles.user_id = users.id', 'type' => 'left']], // JOIN
                          'users.id, users.name, user_profiles.bio' // SELECT fields
                      );

        return $this->response->setJSON($users);
    }

    /**
     * 2. Dynamic ADD Example
     */
    public function insertData()
    {
        // Imagine swapping to a completely different table like 'products'
        $productData = [
            'title' => 'Wireless Mouse',
            'price' => 29.99,
            'stock' => 100
        ];

        $inserted = $this->dynamicModel
                         ->setTargetTable('products')
                         ->addData($productData);

        if ($inserted) {
            return $this->response->setJSON(['status' => 'Success', 'message' => 'Data added successfully!']);
        }
        
        return $this->response->setJSON(['status' => 'Error'])->setStatusCode(400);
    }

    /**
     * 3. Dynamic UPDATE Example
     */
    public function modifyData()
    {
        // Target 'users' table, update email where id = 5
        $whereClause  = ['id' => 5];
        $updateData   = ['email' => 'newemail@example.com'];

        $updated = $this->dynamicModel
                        ->setTargetTable('users')
                        ->updateData($whereClause, $updateData);

        if ($updated) {
            return $this->response->setJSON(['status' => 'Success', 'message' => 'Data updated successfully!']);
        }

        return $this->response->setJSON(['status' => 'Error'])->setStatusCode(400);
    }
}