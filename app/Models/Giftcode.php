<?php

namespace App\Models;

use App\Models\GiftcodeUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giftcode extends Model
{
    use HasFactory;

    public function beUsedByUser($user_id)
    {
        $used = GiftcodeUser::where(["user_id" => $user_id, "giftcode_id" => $this->id])->first();
        return $used ? true : false;
    }
}
