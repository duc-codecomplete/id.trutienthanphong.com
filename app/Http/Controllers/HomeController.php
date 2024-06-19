<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Giftcode;
use App\Models\Donate;
use App\Models\GiftcodeUser;
use DB;

class HomeController extends Controller
{

    public function home()
    {
        return view('home');
    }

    public function signup()
    {
        return view('signup');
    }

    public function signin()
    {
        return view('signin');
    }

    public function signupPost(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|min:4|max:10|alpha_num',
            'passwd' => 'required|min:4|max:10|alpha_num',
            'passwdConfirm' => 'required|same:passwd',
            'email' => 'required|email|unique:users,email',
        ], [
            "login.min" => "Tên đăng nhập chỉ được chứa từ 3 - 10 kí tự",
            "login.max" => "Tên đăng nhập chỉ được chứa từ 3 - 10 kí tự",
            "login.alpha_num" => "Tên đăng nhập chỉ được chứa chữ và số",
            "passwd.min" => "Mật khẩu chỉ được chứa từ 3 - 10 kí tự",
            "passwd.max" => "Mật khẩu chỉ được chứa từ 3 - 10 kí tự",
            "passwd.alpha_num" => "Mật khẩu chỉ được chứa chữ và số",
            "passwdConfirm.same" => "Mật khẩu nhập lại không đúng",
        ]);
        sleep(2);
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'http://38.180.106.91/html/reg.php', ["form_params" => [
            "login" => strtolower($request->login),
            "passwd" => $request->passwd,
            "repasswd" => $request->passwd,
            "email" => $request->login . "@gmail.com",
        ]]);
        $content = json_decode($response->getBody()->getContents(), true);
        if ($content["success"]) {
            $user = new User;
            $user->name = $request->login;
            $user->username = $request->login;
            $user->userid = $content["userid"];
            $user->email = $request->login . "@gmail.com";
            $user->password2 = $request->passwd;
            $user->password = \Hash::make($request->passwd);
            $user->email_verified_at = date("Y-m-d H:i:s");
            $user->save();
            return back()->with("success", "Tạo tài khoản thành công!");
        } else {
            return back()->with("error", "Tên đăng nhập đã tồn tại!");
        }
    }

    public function signinPost(Request $request)
    {
        $validated = $request->validate([
            'login' => 'bail|required',
            'password' => 'bail|required',
        ], [
            "login.required" => "Tên đăng nhập chỉ được chứa từ 3 - 10 kí tự"
        ]);
        $login = [
            'email' => $request->login."@gmail.com",
            'password' => $request->password
        ];
        if (\Auth::attempt($login)) {
            return redirect('/');
        } else {
            return redirect()->back()->with('error', 'Tên đăng nhập hoặc mật khẩu không chính xác');
        }
    }
}
