<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Models\Wallet;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function wallets()
    {
        $this->morphMany(Wallet::class, 'owner');
    }
}
