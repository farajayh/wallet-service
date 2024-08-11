<?php

use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\VerifyWalletsBalance;
use Illuminate\Support\Facades\Schedule;


// Routine to check for wallets with inconsitent balances. Runs daily at 00:00 (midnight)
Schedule::command('wallet:verify-wallets-balance')->daily();