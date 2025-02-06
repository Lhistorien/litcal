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

        $validationResult = $roleModel->updateRole($roleId, $newRoleName);

        if ($validationResult === true) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Erreur de validation',
                'errors' => $validationResult
            ]);
        }
    }    
    public function addRole()
    {
        $roleModel = new RoleModel();
        
        $roleName = $this->request->getPost('roleName');
        
        $result = $roleModel->addRole($roleName);  
        
        if ($result === true) 
        {
            return redirect()->back()->with('success', 'Le rôle a été ajouté avec succès.');
        } elseif (is_array($result)) 
        {
            return redirect()->back()->withInput()->with('errors', $result);
        } else 
        {
            return redirect()->back()->with('error', 'Erreur lors de l\'ajout du rôle.');
        }
    }    
}

