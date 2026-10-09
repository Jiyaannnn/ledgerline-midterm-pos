<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // This Model connects the application to the MySQL users table.
    protected $table         = 'users';
    protected $primaryKey    = 'id';

    // Query results are returned as associative arrays for the user view.
    protected $returnType    = 'array';

    // allowedFields protects other columns from unintended mass assignment.
    protected $allowedFields = ['username', 'full_name', 'avatar', 'password', 'is_archived', 'created_at'];
}
