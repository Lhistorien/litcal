<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\RoleEntity;
use Config\Services;
use App\Validation\EditRoleValidation;
use App\Validation\RoleValidation;

class RoleModel extends Model
{
    protected $table      = 'role';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = RoleEntity::class;
    
    protected $allowedFields = 
    [
        'roleName'
    ];

    public function convertToEntity(array $data): RoleEntity
    {
        return new RoleEntity($data);
    }

    public function updateRole(int $roleId, string $newRoleName)
    {
        $validation = Services::validation();
        $validation->setRules(EditRoleValidation::EditRoleRules(), EditRoleValidation::$EditRoleMessages);

        if (!$validation->run(['newRoleName' => $newRoleName])) {
            return $validation->getErrors();
        }

        $existingRole = $this->find($roleId);
        if (!$existingRole) {
            return false;
        }
    
        return $this->where('id', $roleId) 
                    ->set(['roleName' => $newRoleName]) 
                    ->update();
    }
    
    public function addRole(string $roleName)
    {
        $validation = Services::validation();
        $validation->setRules(RoleValidation::$RoleRules, RoleValidation::$RoleMessages);
    
        if (!$validation->run(['roleName' => $roleName])) {
            return $validation->getErrors();
        }
    
        $data = [
            'roleName' => $roleName
        ];
    
        $builder = $this->db->table($this->table);
        $builder->insert($data);
    
        return true;
    }    
}
