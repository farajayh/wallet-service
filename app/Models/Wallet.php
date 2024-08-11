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

    protected $hidden = ['owner_type'];


    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): hasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
