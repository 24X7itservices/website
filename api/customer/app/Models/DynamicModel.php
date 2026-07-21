<?php

namespace App\Models;

use CodeIgniter\Model;

class DynamicModel extends Model
{
    // We leave this empty so it can be defined dynamically by the controller
    protected $table = ''; 
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * Dynamically change the table this model targets
     */
    public function setTargetTable(string $tableName)
    {
        $this->table = $tableName;
        return $this; // Allows method chaining
    }

    /**
     * DYNAMIC VIEW: Fetch data with optional joins and where clauses
     */
    public function viewData(array $where = [], array $joins = [], string $select = '*')
    {
        $builder = $this->builder($this->table);
        $builder->select($select);

        foreach ($joins as $join) {
            $builder->join($join['table'], $join['cond'], $join['type'] ?? 'inner');
        }

        
        if (!empty($where)) {
            $builder->where($where);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * DYNAMIC ADD: Insert data into the specified table
     */
    public function addData(array $data)
    {
        $builder = $this->builder($this->table);
        return $builder->insert($data); 
    }

    /**
     * DYNAMIC UPDATE: Update data based on a where clause
     */
    public function updateData(array $where, array $data)
    {
        $builder = $this->builder($this->table);
        return $builder->where($where)->update($data);
    }
}