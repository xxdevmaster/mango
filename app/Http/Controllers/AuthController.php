<?php

namespace App\Http\Controllers;

use App\Token;
use Auth;
use Carbon\Carbon;
//use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function login($login, $password)
    {
        $callback = Input::get('callback');

        if ($callback && Auth::attempt(['title' => $login, 'password' => md5($password)])) {
            // Authentication passed...
            $user = Auth::User();

            if($user->id > 0){
                if($user->status >0)
                    echo $callback.'('.json_encode(array('error'=>1,'msg'=>'Wrong Login or Passsword.')).')';
                else{
                    $token = self::getToken($user->id);
                    echo  $callback.'('.json_encode(array('error'=>0,'token'=>$token)).')';
                }
            }
            else
                echo $callback.'('.json_encode(array('error'=>1,'msg'=>'Wrong Login or Passsword.')).')';
        }else{
            echo $callback.'('.json_encode(array('error'=>1,'msg'=>'Wrong Login or Passsword.')).')';
        }
    }

    public function logout(){
        Auth::logout();

        return redirect('http://cinehost-back.loc');
    }

    private function getToken($user_id)
    {
        $token = md5(time().$user_id.rand(1, 5000));
        Token::where('dt', '<', Carbon::now()->subDay(2))->delete();
        Token::create(['dt' => Carbon::now(), 'users_id' => $user_id, 'token' => $token]);

        return $token;
    }



}