<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Char;

class Transaction extends Model
{
    use HasFactory;

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }


    public function getCharName() {
        $char = Char::where("char_id", $this->char_id)->first();
        return $char->name2 ?? $char->name;
    }
}
