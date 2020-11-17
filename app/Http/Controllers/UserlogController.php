<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;

class UserlogController extends Controller
{

    public function store(request $request)
    {

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $subscriperid =$this->gen_uuid();

            echo DB::insert('insert into users (id , name , email , password ,subscriper_id ) VALUES (?,?,?,?,?)', [null, $name, $email, $password , $subscriperid]);

    }

    public function logs(Request $request)
    {
        $email = $request->input('email');
        $pass = $request->input('password');
        $data = DB::select('select id from users where email=? AND password=?', [$email, $pass]);
        if ($data) {
            $request->session()->put('data', $request->input());
            return redirect('profile');
        } else
            echo "wrong email or password";
    }





    public function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}
