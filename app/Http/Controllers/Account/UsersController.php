<?php
namespace App\Http\Controllers\Account;

use App\User;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Libraries\MandrillService\Mandrill;

use App\Libraries\MandrillService\MandrillService;

class UsersController extends Controller
{
    public function listAll(Request $request)
	{
        $current_menu = 'account_users';
		$userData = $this->getUsersListData();
		$userData['current_menu'] = 'account_users';
        return view('account.users', $userData);
    }
	
	
    public function update(Request $request){
        $R = $request->all();
        $allRoles = Role::all()->keyBy('slug');
        $allPremisttions = Permission::all()->keyBy('slug');
        foreach ($R['users'] AS $k => $user_id){
            $user = User::find($user_id);
            $user->detachAllRoles();
            $user->detachAllPermissions();
            if ($R['cms_role_'.$user_id] !==  'custom') {
                $userNewRole = $allRoles[$R['cms_role_' . $user_id]];
                $user->attachRole($userNewRole);
            }
            else {
                if (isset($R['rights'][$user_id])){
                    foreach ($R['rights'][$user_id] AS $k => $permSlug) {
                        $user->attachPermission($allPremisttions[$permSlug]);
                    }
                }

            }


        }
        return json_encode(true);
    }
	
	public function ValidateInputs($request)
	{
		$validator = Validator::make($request->Input(),[
			'cms_role_0'  => 'required',
			'email'       => 'required|email|unique:cc_users,email|unique:cc_users,title',
			'accounts_id' => 'required|integer',
		],
		[
			'email.required' => 'The Email field is required',
			'email.email' => $request->Input('email').' is an invalid email.',
			'email.unique' => $request->Input('email').' user already exists!',
			'cms_role_0.rquired' => 'The Role field is required',
		]
		);
		return $validator;
	}
	
	/*
		Create new user
	*/
	public function create(Request $request)
	{
		
		//validate input fields
		$validator = $this->ValidateInputs($request);
		
		if($validator->fails())
		{		
			$result = array(
				'error'=>1,
				'msg'=>$validator->errors()->all()
			);
			return $result;
		}
		else
		{
			$title=trim(filter_var($request->Input('email'),FILTER_SANITIZE_STRING));
			$email=trim(filter_var($request->Input('email'),FILTER_SANITIZE_STRING));
			$invite_token=md5($email);
			$invite_dt=date("Y-m-d");
			
			
			/* insert table cc_users */			
			$inviteNewUserId=User::create([
				'title' => $title,
				'email' => $email,
				'accounts_id' => trim(filter_var($request->Input('accounts_id'),FILTER_SANITIZE_NUMBER_INT)),
				'invite_dt' => $invite_dt,
				'invite_token' => $invite_token,
			])->id;
			
			
			if($inviteNewUserId > 0)
			{
				$user = Auth::user();
				$userEmail = $user->email;
				$userPerson = $user->person;
				
				/* Set User Role */
				
				$invitedUser = User::find($inviteNewUserId);
				if ($request->Input('cms_role_0') !==  'custom') {
					$allRoles = Role::all()->keyBy('slug');
					$userNewRole = $allRoles[$request->Input('cms_role_0')];
					$invitedUser->attachRole($userNewRole);
				}
				else {
					$rights = $request->Input('rights');
					if (isset($rights[0])){
						$allPremisttions = Permission::all()->keyBy('slug');
						foreach ($rights[0] AS $k => $permSlug) {
							$invitedUser->attachPermission($allPremisttions[$permSlug]);
						}
					}

				}
				
				/*Mandrill mail start*/	
				$data=array(
					'email' => $email,
					'title' => $title,
					'AuthUserEmail' => $userEmail,
					'AuthUserPerson' => $userPerson,
					'invite_token' => $invite_token
				);				
				$MandrillStatus = $this->SendInvitationMail($data);
			
				if( $MandrillStatus == 'sent')			
					$result = array(
						'error'=>0,
						'msg'=>'Invitation has been sent successfully!'
					);	
				else
					$result = array(
						'error'=>0,
						'msg'=>'Please try re send the invitation!'
					);					
			}else{
				$result = array(
					'error'=>1,
					'msg'=>'Database Server Error'
				);				
			}
			return $result;
		}
	}
	public function getUsersListData()
	{	
		
        $user = Auth::user();
		$userId = $user->accounts_id;
        $account_info = $user->account;
        $account_users = $account_info->users()->with('roles')->get();
        foreach($account_users as $account_user){
            $account_user->current = false;
            if ($user->id == $account_user->id)
                $account_user->current = true;
            if (!empty($account_user->getRoles()->first()->slug)){
                $account_user->roleSlug = $account_user->getRoles()->first()->slug;
                $account_user->permissions = json_decode($account_user->roles->first()->permissions->keyBy('slug')->keys());
            }
            else {
                $account_user->roleSlug = 'custom';
                $account_user->permissions = json_decode($account_user->userPermissions()->get()->keyBy('slug')->keys());
            }
        }
        $globalRoles = array();
        $roles = Role::all();
        $roles = $roles->keyBy('slug');
		$permissionsAll = Permission::all();
		$permissionsAll = $permissionsAll->keyBy('slug');
        foreach($roles AS $key => $val){
            $permissions = $val->permissions->keyBy('slug');
            $globalRoles[$key]['permissions'] = $permissions;
            $globalRoles[$key]['info'] = $val;
        }
        $globalRoles['custom']['permissions'] =(object) array();
        $globalRoles['custom']['info'] =  (object)array('slug'=>'custom','name'=>'Custom');		
		
		return compact('current_menu', 'account_users', 'globalRoles','permissionsAll','userId');
	}
	public function getTemplate(Request $request){ 
		return view('account.partials.userslist',$this->getUsersListData());
	}
	
    /**
     * Remove the user.
     *
     * @return  result int  0 or 1
     */
    public function destroy(Request $request)
    {
		$userId = trim(filter_var($request->Input('id'),FILTER_SANITIZE_NUMBER_INT));
		if(User::destroy($userId))
			return 1;
		else
			return 0;
    }
	
	
    /**
     * Re send invitation user.
     *
     * @return  result int  0 or 1
     */
	 
	public function reSendInvitation(Request $request)
	{
		$userId = (integer)trim(filter_var($request->Input('id'),FILTER_SANITIZE_NUMBER_INT));
		$invite_token = md5(time());
		
		
		//update the invite_dt and invite_token
		$userUpdate =  User::where('id', $userId)->update(array(
			'invite_dt' => date("Y-m-d"),
			'invite_token' => $invite_token,
		));

		if($userUpdate)
		{
			$AuthUser_info = Auth::user();
			$AuthUserEmail = $AuthUser_info->email;
			$AuthUserPerson = $AuthUser_info->person;
			$AuthUserId = trim(filter_var($request->Input('id'),FILTER_SANITIZE_NUMBER_INT));	
			$user_info = User::all()->where('id',$userId)->all();	
			foreach($user_info as $val)
			{
				$email = $val->email;
				$title = $val->title;
			}
			
			$data=array(
				'email' => $email,
				'title' => $title,
				'AuthUserEmail' => $AuthUserEmail,
				'AuthUserPerson' => $AuthUserPerson,
				'invite_token' => $invite_token
			);

			/*Mandrill mail start*/	
			$MandrillStatus = $this->SendInvitationMail($data);

			if($MandrillStatus == 'sent')
				return 1;
			else 
				return 0;
		}else
			return 0;
	}
	
	public function SendInvitationMail($data)
	{
		$mandrill = new Mandrill("zrVZzzehpLYYFcnHkvegGw");

		$message = array(
			'subject' => 'Invitation to  Cinehost, Inc. ',
			'from_email' => 'noreply@cinehost.com',
			'html' => '',
			'to' => array(array('email' => $data['email'], 'name' => ' ')),
			'merge_vars' => array(array(
				'rcpt' => $data['email']
			)));
		
		$template_name = 'CinehostUserInvitation';
		$template_content = array(
			array(
				'name' => 'inviterMail',
				'content' => '<a href="mailto:'.$data['AuthUserEmail'].'">'.$data['AuthUserPerson'].' '.(empty($data['AuthUserEmail'])?'':' ('.$data['AuthUserEmail'].') ').' </a>'
			   ),
			array(
				'name' => 'vod-platform-name',
				'content' => $data['title']
			   ),
			array(
				'name' => 'exDate',
				'content' => date("M d,Y",strtotime("+1 month", strtotime(date("Y-m-d"))))
			   ),
			array(
				'name' => 'invintationLink',
				'content' => '<a href="'.url().'/userInvitation/'.$data['invite_token'].'">'.url().'/userInvitation/'.$data['invite_token'].'</a>'
			   )
		);

		return $MandrillStatus = $mandrill->messages->sendTemplate($template_name, $template_content, $message)[0]['status'];		
	}
}
