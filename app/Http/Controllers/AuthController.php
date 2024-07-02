<?php

namespace App\Http\Controllers;

use App\Models\Char;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signup()
    {
        if (Auth::check()) {
            return redirect("/");
        }
        return view('signup');
    }

    public function signin()
    {
        if (Auth::check()) {
            return redirect("/");
        }
        return view('signin');
    }

    public function signupPost(Request $request)
    {
        $validated = $request->validate([
            'login' => 'bail|required|min:4|max:10|alpha_num|unique:users,username',
            'passwd' => 'bail|required|min:4|max:10|alpha_num',
            'passwdConfirm' => 'bail|required|same:passwd',
            'email' => 'bail|required|email|unique:users,email',
        ], [
            "login.min" => "Tên đăng nhập chỉ được chứa từ 3 - 10 kí tự",
            "login.max" => "Tên đăng nhập chỉ được chứa từ 3 - 10 kí tự",
            "login.alpha_num" => "Tên đăng nhập chỉ được chứa chữ và số",
            "login.unique" => "Tên đăng nhập đã được sử dụng",
            "passwd.min" => "Mật khẩu chỉ được chứa từ 3 - 10 kí tự",
            "passwd.max" => "Mật khẩu chỉ được chứa từ 3 - 10 kí tự",
            "passwd.alpha_num" => "Mật khẩu chỉ được chứa chữ và số",
            "passwdConfirm.same" => "Mật khẩu nhập lại không đúng",
        ]);
        sleep(2);
        $content = $this->callGameApi("POST", "/html/reg.php", [
            "login" => strtolower($request->login),
            "passwd" => $request->passwd,
            "repasswd" => $request->passwd,
            "email" => $request->login . "@gmail.com",
        ]);
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
            $this->sendMessage("Người chơi ". $request->login." vừa đăng ký tài khoản");
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
            "login.required" => "Tên đăng nhập chỉ được chứa từ 3 - 10 kí tự",
        ]);
        $login = [
            'username' => $request->login,
            'password' => $request->password,
        ];
        if (\Auth::attempt($login)) {
            if (\Auth::user()->role != "member") {
                \Auth::logout();
                return redirect()->back()->with('error', 'Thông tin đăng nhập không chính xác');
            }
            return redirect('/');
        } else {
            return redirect()->back()->with('error', 'Thông tin đăng nhập không chính xác');
        }
    }

    public function setMainChar()
    {
        $user = Auth::user();
        $user->main_id = request()->main_id;
        $user->save();
        return back();
    }

    public function setMainCharHome($id)
    {
        $user = Auth::user();
        $user->main_id = $id;
        $user->save();
        return back();
    }

    public function updateChar()
    {
        $this->charUpdate();
        return back();
    }

    private function charUpdate()
    {
        $response = $this->callGameApi("get", "/html/char_update.php", []);
        $data = $response["data"];
        $chars = [];
        foreach ($data as $user) {
            array_push($chars, [
                "userid" => $user["akkid"],
                "char_id" => $user["id"],
                "name" => $user["name"],
                "gender" => $user["gender"] == "0" ? "Nam" : "Nữ",
                "pk_value" => $user["pkvalue"],
                "class" => $user["occupation"],
                "level" => $user["level"],
                "reputation" => $user["reputation"],
            ]);
        }
        Char::upsert($chars, ['char_id', 'userid'], ['name', "pk_value", "gender", "class", "level", "reputation"]);
        return $data;
    }

    public function updateCharApi()
    {
        $data = $this->charUpdate();
        $this->setOnline();
        return response()->json($data);
    }

    private function setOnline()
    {
        $response = $this->callGameApi("get", "/html/online1.php", []);
        $data = $response["data"];
        $onlines = collect($data)->pluck('ID')->all();
        User::whereIn('userid', $onlines)->update(['is_online' => true]);
        User::whereNotIn('userid', $onlines)->update(['is_online' => false]);
    }

    public function getPassword()
    {
        return view("password");
    }

    public function postPassword(Request $request)
    {
        $validated = $request->validate([
            'old' => 'bail|required',
            'new' => 'bail|required|min:4|max:10|alpha_num',
            'newcf' => 'bail|required|same:new',
        ], [
            "new.min" => "Mật khẩu chỉ được chứa từ 3 - 10 kí tự",
            "new.max" => "Mật khẩu chỉ được chứa từ 3 - 10 kí tự",
            "new.alpha_num" => "Mật khẩu chỉ được chứa chữ và số",
            "newcf.same" => "Mật khẩu xác thực không giống nhau",
        ]);
        $user = \Auth::user();
        if ($request->old == $user->password2) {
            try {
                DB::beginTransaction();
                $this->callGameApi("POST", "/html/passwdapi.php", [
                    "login" => $user->username,
                    "passwd" => $request->new,
                ]);
                $user->password2 = $request->new;
                $user->password = \Hash::make($request->new);
                $user->change_pass = $user->change_pass + 1;
                $user->save();
                DB::commit();
                return back()->with("success", "Đổi mật khẩu thành công!");
            } catch (\Throwable $th) {
                DB::rollback();
                return back()->with("error", "Đã có lỗi xảy ra, vui lòng liên hệ với GM!");
            }
        }
        return back()->with("error", "Mật khẩu hiện tại không đúng!");
    }

    public function changeClassGet($id)
    {
        $char = Char::where("char_id", $id)->first();
        return view("class", ["char" => $char]);
    }

    public function changeClassPost($id)
    {
        if (request()->class < 100) {
            return back()->with("error", "Vui lòng chọn môn phái!");
        }
        $user = Auth::user();
        if ($user->balance < 100) {
            return back()->with("error", "Số xu trong tài khoản không đủ!");
        }
        $char = Char::where("char_id", $id)->first();
        $this->callGameApi("post", "/html/send2.php", [
            "receiver" => $id,
            "itemid" => request()->class,
            "count" => 1,
        ]);
        $user->balance = intval($user->balance) - 100;
        $user->save();
        return back()->with("success", "Yêu cầu thành công!");
    }

    public function updateNameApi()
    {
        $chars = Char::all();
        $chars = collect($chars)->filter(function ($value) {
            return $value->name2 == "" && $this->specialChars($value->name);
        })->values();
        if (count($chars) > 0) {
            $this->sendMessage("Có nhân vật cần update tên tiếng Việt ! https://admin.trutien.vn");
        }
        return $chars;
    }

    public function bot()
    {
        $this->sendMessage("Có nhân vật cần update tên tiếng Việt ! https://admin.trutien.vn");
        return "ok";
    }

    public function cache()
    {
        \Artisan::call('clear-compiled');
        return "ok";
    }


    private function specialChars($str)
    {
        return preg_match('/[^a-zA-Z0-9\.]/', $str) > 0;
    }
}
