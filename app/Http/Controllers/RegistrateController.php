<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\User; //модель пользоватля
use App\UserSocialAccount;
use App\ConfirmUsers; //модель пользоватля
use Mail; // фасад для отправки почты
use Auth;
use Illuminate\Support\Facades\Session;


class RegistrateController extends Controller
{

    public function checkEmailFree(Request $request){
        $response = array();
        $input = $request->only(['email']);
        $user = User::where('email', '=', $request->input('email'))->first();
        if ($user === null) {
            $response['status'] = 0;
            $response['info'] = 'Email free';
        }
        else{
            $response['status'] = 1;
            $response['info'] = 'User with this email already exist';
        }

        return response($response, 200);

    }

    public function register(Request $request)
    {
        //Getting user data
        $input = $request->only(['first_name', 'last_name', 'user_type', 'email', 'password', 'social_account_id']);

        //Insert user
        $user = User::where('email', '=', $request->input('email'))->first();
        if ($user !== null) {
            die('User with this email already exist');
        }

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'user_type' => $request->input('user_type'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        if(!empty($user->id))
        {
            $email = $user->email;
            $token = str_random(32);    //token for email cerify
            $model = new ConfirmUsers;  //создаем экземпляр нашей модели
            $model->email = $email;     //вставляем в таблицу email
            $model->token = $token;     //вставляем в таблицу токен
            $model->save();         // сохраняем все данные в таблицу


            if($request->input('social_account_id')){
                $socialAccount = UserSocialAccount::whereId($request->input('social_account_id'))
                    ->whereUserId(null)
                    ->first();
                $socialAccount->user()->associate($user);
                $socialAccount->save();
            }

            //отправляем ссылку с токеном пользователю
            Mail::send('emails.confirm',['token'=>$token],function($u) use ($user)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($user->email);
                $u->subject('Confirm registration');
            });

            $responseCode = 200;
            $responseData['error'] = false;
            $responseData['status'] = 0;
            $responseData['info'] = 'User successfully registered';
        }
        else {
            die('Something went wrong. Please try again later.');
        }

        $emailArr = explode('@', $user->email);
        $emailUrl = 'http://'.$emailArr[1];

        return view('pages.success', ['emailUrl' => $emailUrl]);
    }

    public function confirm($token)
    {
        $model = ConfirmUsers::where('token','=',$token)->first();
        if(!count($model)){
            die('Token not found!');
        }
        else{
            $user = User::where('email','=',$model->email)->first(); //выбираем пользователя почта которого соответствует переданному токену
            $user->email_confirmed = 1; // меняем статус на 1
            $user->save();  // сохраняем изменения
            $model->delete(); //Удаляем запись из confirm_users

            Auth::login($user);
            
            if($user->user_type == 'employee'){
                return redirect('/account');
            }
            else{
                //If buyer come to login from card buy process - redirect him to that card
                if(Session::get('formJob')){
                    $jobID = Session::get('formJob');
                    Session::forget('formJob');
                    return redirect('/job/'.$jobID);
                }

                return redirect('/');
            }
        }
    }
}
