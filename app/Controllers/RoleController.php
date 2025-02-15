<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Controllers\BaseController;

class RoleController extends BaseController
{
    public function updateRole()
    {
        $roleModel = new RoleModel();

        $roleId = $this->request->getPost('roleId');  
        $newRoleName = $this->request->getPost('newRoleName');

        $result = $roleModel->updateRole($roleId, $newRoleName);

        if ($result === true) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Erreur de validation',
                'errors' => $result
            ]);
        }
    }    

    public function addRole()
    {
        $roleModel = new RoleModel();
        
        $roleName = $this->request->getPost('roleName');
        
        $result = $roleModel->addRole($roleName);
        
        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }
        
        return redirect()->to('/dashboard#roles')->with('success', 'Le rôle a été ajouté avec succès.');
    }    
}
