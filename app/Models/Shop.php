<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    public function getSell()
    {
        return Transaction::where("shop_id", $this->id)->sum("shop_quantity");
    }
}
