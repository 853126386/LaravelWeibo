<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index','confirmEmail']
        ]);
    }

    public function index(){
        $users=User::paginate(10);

        return view('users.index',compact('users'));
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */


    public function create(){
        return view('users/create');
    }
    public function show(User $user){
        return view('users/show',compact('user'));

    }

    public function store(Request $request){

        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|max:6',
        ]);
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        $this->sendEmailConfirmTo($user);
//        Auth::login($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return  redirect()->route('users.show',[$user]);
    }


    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user){
        $this->authorize('update',$user);
        return  view('users.edit',compact('user'));
    }

    /**
     * @param User $user
     * @param Request $request
     */
    public function update(User $user,Request $request){
        $this->authorize('update',$user);
        $this->validate($request,[
           'name'=>'required|max:50',
           'password'=>'required|confirmed|min:6',
        ]);
        $data=['name'=>$request->name];
        if($request->password){
            $data['password']=bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','编辑资料成功');
        return redirect()->route('users.show',$user->id);
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    public function confirmEmail($token){
        $user=User::where('activation_token',$token)->firstOrFail();
        $user->activated=true;
        $user->activation_token=null;
        $user->save();


        Auth::login($user);
        session()->flash('success','恭喜您，激活成功');
        return redirect()->route('users.show',$user->id);
    }

    public function sendEmailConfirmTo($user){
        $view='emails.confirm';
        $data=compact('user');
        $from='aaronLee@qq.com';
        $name='aaronLee';
        $to=$user->email;
        $subject='感谢注册web应用';
        Mail::send($view,$data,function($message) use ($from,$name,$to,$subject){
            $message->from($from,$name)->to($to)->subject($subject);
        });
    }

}
