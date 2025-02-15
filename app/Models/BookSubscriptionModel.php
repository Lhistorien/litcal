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
            'book'   => $bookId,
            'user'   => $userId,
            'status' => 1
        ];

        return $this->insert($data);
    }

    public function getUserSubscriptionsWithAuthors($userId)
    {
        $builder = $this->db->table('BookSubscription as bs');
        $builder->select('
            bs.id as subscriptionId,
            b.id as bookId, 
            b.title, 
            b.cover, 
            b.publication, 
            p.publisherName, 
            GROUP_CONCAT(a.authorName SEPARATOR ", ") as authors
        ');
        
        $builder->join('Book as b', 'b.id = bs.book');
        $builder->join('Publisher as p', 'p.id = b.publisher');
        $builder->join('BookAuthor as ba', 'ba.book = b.id', 'left');
        $builder->join('Author as a', 'a.id = ba.author', 'left');
        
        $builder->where('bs.user', $userId);
        $builder->where('bs.status', 1);
        $builder->groupBy('b.id');
        
        $query = $builder->get();
        return $query->getResult();
    }
    public function unsubscribe($subscriptionId, $userId)
    {
        return $this->update($subscriptionId, ['status' => 0]);
    } 
    public function toggleSubscription($bookId, $userId)
    {
        // Recherche d'une souscription existante
        $subscription = $this->where('book', $bookId)
                             ->where('user', $userId)
                             ->first();
    
        if ($subscription) {
            // S'il y en a déjà une, on change le statut
            $newStatus = ($subscription['status'] == 1) ? 0 : 1;
            $this->update($subscription['id'], ['status' => $newStatus]);
    
            return [
                'success' => true,
                'message' => ($newStatus == 1) ? 'Vous suivez désormais ce livre.' : 'Vous ne suivez plus ce livre.',
                'action'  => ($newStatus == 1) ? 'follow' : 'unfollow'
            ];
        } else {
            // Si pas, on crée une nouvelle souscription avec status = 1
            $data = [
                'book'   => $bookId,
                'user'   => $userId,
                'status' => 1
            ];
    
            if ($this->insert($data)) {
                return [
                    'success' => true,
                    'message' => 'Vous suivez désormais ce livre.',
                    'action'  => 'follow'
                ];
            } else {
                $errors = $this->errors();
                return [
                    'success' => false,
                    'message' => 'Erreur lors du suivi du livre: ' . implode(', ', $errors)
                ];
            }
        }
    }    
}
