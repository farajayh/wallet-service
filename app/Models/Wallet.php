<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Models\Transaction;

class Wallet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'currency'];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): hasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getOwnerTypeAttribute($value)
    {
        switch ($value) {
            case \App\Models\Customer::class:
                return 'Customer';
            case \App\Models\Merchant::class:
                return 'Merchant';
            default:
                return '';
        }
    }
}
