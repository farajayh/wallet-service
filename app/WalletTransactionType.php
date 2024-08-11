<?php

namespace App;

enum WalletTransactionType: string
{
    case DEBIT = 'DEBIT';
    case CREDIT = 'CREDIT';
}
