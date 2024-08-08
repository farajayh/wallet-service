<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Wallet;

class Merchant extends Model
{
    use HasFactory;

    public function wallets()
    {
        $this->morphMany(Wallet::class, 'owner');
    }
}
