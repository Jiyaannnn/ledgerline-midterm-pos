<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    // This Model maps CodeIgniter operations to the MySQL customers table.
    protected $table         = 'customers';
    protected $primaryKey    = 'id';

    // Returning arrays lets the existing foreach view use familiar field keys.
    protected $returnType    = 'array';

    // Only these columns may be inserted or updated through this Model.
    protected $allowedFields = ['full_name', 'email', 'phone', 'is_archived', 'created_at'];
}
