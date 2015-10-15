<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;


class UserInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($tk, Request $request)
    {
        $R = $request->all();
        $isErr = true;
        if($tk)
        {

            $tk = strtr(addslashes($tk),array('_' => '\_', '%' => '\%'));
            $user = User::all()->where('invite_token',$tk);
            if(isset($user->first()->id)) {
                $isErr = false;
            }
            else
                $isErr = true;
        }
        else
        {
            $isErr = true;
        }

        return view('userInvitation.partials.userInvitationForm',compact('tk','isErr'));
    }
    function register(Request $request){
        $R = $request->all();

        $tk = $R['tk'];
            $tk = strtr(addslashes($tk),array('_' => '\_', '%' => '\%'));
            $user = User::all()->where('invite_token',$tk);
            if(isset($user->first()->id)) {
                $safe_name = strtr(addslashes($R['lname']),array('_' => '\_', '%' => '\%'));
                $safe_surname = strtr(addslashes($R['lsurname']),array('_' => '\_', '%' => '\%'));
                $safe_password = strtr(addslashes($R['pwd']),array('_' => '\_', '%' => '\%'));
                User::where('id',$user->first()->id)->update(array('status'=>0, 'invite_token'=>'','pass'=>md5($safe_password),'passclean'=>$safe_password,'person'=>$safe_name.' '.$safe_surname,));
                Auth::attempt(['title' => $user->first()->title, 'password' => md5($safe_password)]);
               return redirect('/');
            }
        return redirect('/userInvitation/'.$tk);


    }

}
