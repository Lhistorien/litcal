<?php

namespace App\Models;

use CodeIgniter\Model;

class LabelSubscriptionModel extends Model
{
    protected $table         = 'LabelSubscription';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user', 'label', 'status'];
    protected $returnType    = 'object';

    public function toggleSubscription($userId, $labelId)
    {
        // Déduire le type de label à partir du préfixe
        $prefix = substr($labelId, 0, 2);
        switch ($prefix) {
            case 'AU':
                $entity = "cet auteur";
                break;
            case 'PU':
                $entity = "cet éditeur";
                break;
            case 'SE':
                $entity = "cette série";
                break;
            case 'GE':
                $entity = "ce genre";
                break;
            case 'SG':
                $entity = "ce sous-genre";
                break;
            default:
                $entity = "ce label";
        }
        
        // Recherche d'une souscription existante
        $subscription = $this->where('user', $userId)
                             ->where('label', $labelId)
                             ->first();
    
        if ($subscription) {
            if ($subscription->status == 1) {
                // L'utilisateur est déjà abonné, on désabonne
                $this->update($subscription->id, ['status' => 0]);
                return [
                    'success'    => true,
                    'message'    => "Vous n’êtes plus abonné à $entity.",
                    'action'     => 'unsubscribed',
                    'subscribed' => false
                ];
            } else {
                // Il était désabonné, on réactive la souscription
                $this->update($subscription->id, ['status' => 1]);
                return [
                    'success'    => true,
                    'message'    => "Vous êtes abonné à $entity.",
                    'action'     => 'subscribed',
                    'subscribed' => true
                ];
            }
        } else {
            // Aucune souscription n'existe, on en crée une avec status = 1
            $data = [
                'user'   => $userId,
                'label'  => $labelId,
                'status' => 1
            ];
            if ($this->insert($data)) {
                return [
                    'success'    => true,
                    'message'    => "Vous êtes abonné à $entity.",
                    'action'     => 'subscribed',
                    'subscribed' => true
                ];
            } else {
                $errors = $this->errors();
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la souscription: ' . implode(', ', $errors)
                ];
            }
        }
    }    

    public function isSubscribed($userId, $labelId)
    {
        $subscription = $this->where('user', $userId)
                             ->where('label', $labelId)
                             ->first();
        return ($subscription && $subscription->status == 1);
    }

    public function getUserLabelSubscriptions($userId)
    {
        $builder = $this->db->table('LabelSubscription as ls');
        $builder->select('ls.id as subscriptionId, ls.label, l.labelName, ls.status');
        $builder->join('label as l', 'l.id = ls.label');
        $builder->where('ls.user', $userId);
        $builder->where('ls.status', 1);
        $query = $builder->get();
        return $query->getResult();
    }
}
