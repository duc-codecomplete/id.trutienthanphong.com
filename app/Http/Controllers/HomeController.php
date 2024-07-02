<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Giftcode;
use App\Models\GiftcodeUser;
use App\Models\Promotion;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class HomeController extends Controller
{

    public function home()
    {
        $user = Auth::user();
        $shops = Transaction::where("type", "shop")->latest()->limit(10)->get();
        return view('home', ["user" => $user, "shops" => $shops]);
    }

    public function shopHistory()
    {
        $shops = Transaction::where("type", "shop")->latest()->get();
        return view('histories', ["shops" => $shops]);
    }

    public function getNapTien()
    {
        return view("naptien");
    }

    public function getQrcode()
    {
        return view("qrcode", ["cash" => request()->cash]);
    }

    public function getQrcodeSuccess()
    {
        return back();
    }

    public function getShop()
    {
        $shops = Shop::where("status", "active")->get();
        return view("shop", ["shops" => $shops]);
    }

    public function postShop(Request $request)
    {
        $user = Auth::user();
        if ($user->main_id == "") {
            return redirect()->back()->with('error', 'Chưa chọn nhân vật để mua vật phẩm.');
        }

        if ($request->quantity < 1) {
            return redirect()->back()->with('error', 'Số lượng không thể nhỏ hơn 1.');
        }

        $shop = Shop::find($request->shop_id);
        if ($request->quantity > $shop->stack) {
            return redirect()->back()->with('error', 'Số lượng không thể lớn hơn số lượng xếp chồng của vật phẩm.');
        }
        $balance = $user->balance;
        $cash = $request->quantity * $shop->price;
        if ($balance < $cash) {
            return redirect()->back()->with('error', 'Số xu trong tài khoản không đủ (cần ' . $cash . ' xu, thiếu ' . $cash - $balance . ' xu), vui lòng nạp thêm.');
        }
        try {
            DB::beginTransaction();
            $this->callGameApi("post", "/html/send2.php", [
                "receiver" => $user->main_id,
                "itemid" => $shop->itemid,
                "count" => $request->quantity,
            ]);
            $user->balance = $balance - $cash;
            $user->save();

            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->shop_quantity = $request->quantity;
            $transaction->shop_id = $request->shop_id;
            $transaction->type = "shop";
            $transaction->char_id = $user->main_id;
            $transaction->save();
            DB::commit();
            return back()->with('success', 'Chúc mừng bạn đã mua thành công ' . $request->quantity . ' cái ' . $shop->name . ' với giá ' . $cash . ' (xu)');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with("error", "Có lỗi xảy ra, vui lòng liên hệ GM!");
        }
    }

    public function getGiftcode()
    {
        $giftcodes = Giftcode::all();
        return view("giftcodes", ["giftcodes" => $giftcodes]);
    }

    public function useGiftcode(Request $request)
    {
        $char_id = request()->char_id;
        $giftcode = request()->giftcode;
        $user = Auth::user();
        if (!$char_id) {
            return back()->with("error", "Vui lòng vào game tạo nhân vật!!");
        }
        $code = Giftcode::where("giftcode", $giftcode)->first();
        if (!$code) {
            return redirect()->back()->with('error', 'Giftcode không tồn tại');
        }
        $today = \Carbon\Carbon::now()->toDateString();
        if ($today > $code->expired) {
            return redirect()->back()->with('error', 'Giftcode này đã hết hạn');
        }
        $userGiftcode = GiftcodeUser::where(["user_id" => $user->id, "giftcode_id" => $code->id])->first();
        if ($userGiftcode) {
            return redirect()->back()->with('error', 'Bạn đã dùng giftcode này!');
        }
        try {
            DB::beginTransaction();
            $use = new GiftcodeUser;
            $use->user_id = $user->id;
            $use->giftcode_id = $code->id;
            $use->char_id = $char_id;
            $use->save();
            $code->count = $code->count + 1;
            $code->save();

            $this->callGameApi("post", "/html/send2.php", [
                "receiver" => $char_id,
                "itemid" => $code->itemid,
                "count" => $code->quantity,
            ]);
            DB::commit();
            return back()->with("success", "Sử dụng giftcode thành công, vui lòng check tín sứ!");
        } catch (\Exception $e) {
            throw $e;
            DB::rollback();
            return back()->with("error", "Có lỗi xảy ra, vui lòng liên hệ GM!");
        }
    }

    public function getKnb()
    {
        return view("knb");
    }

    public function postKnb()
    {
        $ratio = 3;
        $user = Auth::user();
        $xu = request()->cash;
        if ($xu < 50000 || $xu > $user->balance) {
            return back()->with("error", "Số xu nạp phải lớn hơn 50000 và nhỏ hơn số dư xu hiện có!");
        }
        try {
            DB::beginTransaction();
            $this->callGameApi("POST", "/html/knb.php", [
                "userid" => $user->userid,
                "cash" => intval($xu / 10) * $ratio,
            ]);
            $user->balance = intval($user->balance) - $xu;
            $user->save();

            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->knb_amount = $xu;
            $transaction->type = "knb";
            $transaction->save();
            return back()->with("success", "Đã chuyển " . intval($xu / 1000) * $ratio . " KNB vào game thành công!");
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
            return back()->with("error", "Có lỗi xảy ra, vui lòng liên hệ GM!");
        }
    }

    public function transactions()
    {
        $shops = Transaction::where("user_id", Auth::user()->id)->where("type", "shop")->latest()->get();
        $knbs = Transaction::where("user_id", Auth::user()->id)->where("type", "knb")->latest()->get();
        return view("transactions", ["shops" => $shops, "knbs" => $knbs]);
    }

    public function online()
    {

        $response = $this->callGameApi("get", "/html/online1.php", []);
        $data = $response["data"];
        $onlines = collect($data)->pluck('uid')->all();
        $chars = User::whereIn("userid", $onlines)->get();
        return $chars;
    }

    public function vip()
    {
        try {
            $response = $this->callGameApi("get", "/html/vip.php", []);
            $data = $response["data"];
            return view("vip", ["vips" => $data]);
        } catch (\Throwable $th) {
            return view("vip", ["vips" => []]);
        }
    }

    public function chat()
    {
        try {
            $response = $this->callGameApi("get", "/ch.php", []);
            $data = $response["data"];
            return view("chat", ["chat" => $data]);
        } catch (\Throwable $th) {
            return view("chat", ["chat" => []]);
        }
    }

    public function paymentSuccess(Request $request)
    {
        try {
            $code = $request->code;
            $username = strtolower(substr($code, 2));
            $user = User::where("username", $username)->first();
            $amount = $request->transferAmount;
            $amount_promotion = $amount;
            $processing_time = $request->transactionDate;
            $bank = $request->gateway;

            $trans = new Deposit;
            $trans->user_id = $user->id ?? null;
            $trans->sepay_tran_id = $request->id;
            $trans->amount = $amount;
            $trans->status = "success";
            $trans->processing_time = $processing_time;
            $trans->bank = $bank;
            $trans->account_number = $request->accountNumber;

            $currentPromotion = $this->getCurrentPromotion();
            if ($currentPromotion) {
                if ($currentPromotion->type == "double") {
                    $amount_promotion = $amount_promotion * $currentPromotion->amount;
                } else {
                    $amount_promotion = $amount_promotion + $amount_promotion * $currentPromotion->amount / 100;
                }
            }
            $trans->amount_promotion = $amount_promotion;
            $user->balance = $amount_promotion;
            $trans->save();
            $user->save();
            $msg = "Người chơi ". $username . "đã nạp ".number_format($amount) . "";
            $this->sendMessage($msg);

            return response()->json("ok", 200);
        } catch (\Throwable $th) {
            return view("chat", ["chat" => []]);
        }
    }

    public function histories()
    {
        $histories = Deposit::where("user_id", Auth::user()->id)->latest()->get();
        return view("deposit_history", ["histories" => $histories]);
    }

    private function getCurrentPromotion()
    {
        $now = Carbon::now();
        $currentPromotion = Promotion::where('start_time', '<=', $now)->where('end_time', '>=', $now)->first();
        return $currentPromotion;
    }
}
