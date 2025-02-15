<?php

namespace App\Controllers;

use App\Models\LabelSubscriptionModel;
use App\Models\LabelModel;
use App\Controllers\BaseController;

class LabelController extends BaseController
{
    public function subscribeLabel()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Vous devez être connecté.'
            ]);
        }

        $labelId = $this->request->getPost('label');
        if (!$labelId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Label manquant.'
            ]);
        }

        $model = new LabelSubscriptionModel();
        $result = $model->toggleSubscription($userId, $labelId);

        if ($result['success']) {
            return $this->response->setJSON($result);
        } else {
            return $this->response->setStatusCode(500)->setJSON($result);
        }
    }

    public function checkLabelSubscription()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(403)->setJSON(['subscribed' => false]);
        }

        $labelId = $this->request->getPost('label');
        if (!$labelId) {
            return $this->response->setStatusCode(400)->setJSON(['subscribed' => false]);
        }

        $model = new LabelSubscriptionModel();
        $subscribed = $model->isSubscribed($userId, $labelId);

        return $this->response->setJSON(['subscribed' => $subscribed]);
    }
    
    public function getEnrichedLabels()
    {
        // Deux possibilités : récupérer les labels via POST ou récupérer tous les labels
        $labelIds = $this->request->getPost('labelIds');
        $labelModel = new LabelModel();
        if ($labelIds && is_array($labelIds) && !empty($labelIds)) {
            $labels = $labelModel->getLabelsByIds($labelIds);
        } else {
            $labels = $labelModel->findAll();
        }

        $userId = session()->get('user_id');
        if ($userId) {
            $subscriptionModel = new LabelSubscriptionModel();
            foreach ($labels as $label) {
                // Enrichit chaque label avec la propriété 'subscribed'
                $label->subscribed = $subscriptionModel->isSubscribed($userId, $label->id);
            }
        }

        return $this->response->setJSON($labels);
    }
}
