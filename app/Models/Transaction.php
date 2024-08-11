<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Models\Wallet;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
