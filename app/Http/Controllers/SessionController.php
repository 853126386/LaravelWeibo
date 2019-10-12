<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class SessionController extends Controller
{
    //

    public function create(){
        return view('session.create');
    }

    public function store(Request $request){
        $credentials=$this->validate($request,[
           'email'=>'required|email|max:255',
           'password'=>'required',
        ]);
        if (Auth::attempt($credentials,$request->has('remember'))) {
            session()->flash('success','欢迎回来');
            return redirect()->route('users.show',[Auth::user()]);
            // 登录成功后的相关操作
        } else {
            session()->flash('danger','很抱歉！你的邮箱或者密码错误');
            return redirect()->back()->withInput();
            // 登录失败后的相关操作
        }
        return ;
    }

    public function destroy (){

        Auth::logout();
        session()->flash('success','你已成功退出');
        return redirect('login ');
    }

}

