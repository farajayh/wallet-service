<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Models\Wallet;

class Merchant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'email', 'phone_no', 'address', 'brand_name',
        'brand_description', 'city', 'state', 'country', 'postal_code'
    ];

    public function wallets()
    {
        return $this->morphMany(Wallet::class, 'owner');
    }
}
