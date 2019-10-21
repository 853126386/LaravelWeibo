<?php

use Illuminate\Database\Seeder;
use App\Models\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
//        $user=factory(User::class)->times(50)->make();
//        User::insert($user->makeVisible(['password','remember_token'])->toArray());
        $user=User::where('email','1@qq.com')->first();
//        $user->email='1@qq.com';
        $user->password=bcrypt(123456);
        $user->save();
    }
}
