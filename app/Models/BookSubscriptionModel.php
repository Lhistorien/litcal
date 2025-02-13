<?php

namespace App\Models;

use CodeIgniter\Model;

class BookSubscriptionModel extends Model
{
    protected $table = 'BookSubscription';
    protected $primaryKey = 'id'; 
    protected $allowedFields = ['book', 'user', 'status'];

    public function subscribe($bookId, $userId)
    {
        $data = [
            'book' => $bookId,
            'user' => $userId,
            'status' => 1
        ];

        return $this->insert($data);
    }
}
