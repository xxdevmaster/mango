<?php
namespace App\Libraries\CHpermissions;

use Auth;

class CHpermissions {
	private $authUser;
	
	private $authAccount;
	
	public function __construct()
	{
		$this->authUser = Auth::user();
		$this->authAccount = $this->authUser->account;		
	}
	
	public function isCP()
	{
		return (!empty($this->authAccount->companies_id) && empty($this->authAccount->platforms_id));
	}

    public function isPL(){
		return (empty($this->authAccount->companies_id) && !empty($this->authAccount->platforms_id));
    }
    public function isCPPL(){
        return (!empty($this->authAccount->companies_id) && !empty($this->authAccount->platforms_id));
    }

    public function isTrueCP(){
        return !empty($this->authAccount->companies_id);
    }

    public function isTruePL(){
        return !empty($this->authAccount->platforms_id);
    }
    /**
    * Return the permission of the action 
    *
    * @param  string $actType 'read', 'create', 'update','superadmin' 
    * @param  string $operand 'rights'(true = 0,1) , 'rights_superadmin','metadata' (true = 0,1,2) , 'rights_metadata' (true = 1,2 ), 'movie' (true = ?) 
    * @param  int    $role the value of the role
    * @return bool
    */ 
    public function isAllowAct($act,$operand,$role){
            if ($operand == 'rights'){
                if ($act == 'superadmin'){
                    if ($role==0)return true;
                    else return false;
                }
                else if ($act == 'read'){
                    if ($role<4)return true;
                    else return false;
                }
                else if ($act == 'update'){
                    if ($role<2)return true;
                    else return false;
                }
            }
        return false;
    }	
	
}