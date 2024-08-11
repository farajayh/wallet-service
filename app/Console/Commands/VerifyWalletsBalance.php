<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;

class VerifyWalletsBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:verify-wallets-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // set the output file name. output is saved to a csv file
        $fileName = 'Inconsistent_wallets_' . now()->format('Y_m_d_His') . '.csv';
        $filePath = storage_path('app/' . $fileName);


        $inconsistencyFound = false;
        $count = 0;

        try{
            // create output file
            $file = fopen($filePath, 'w');
            if ($file === false) {
                $this->error('Failed to create CSV file.');
                return;
            }
            
            // write header row to the output file (csv format)
            fputcsv($file, ['Wallet ID', 'Owner ID', 'Owner Type', 'Expected Balance', 'Actual Balance', 'Balance Difference']);

            // loop through wallets in chunks of 500 records at a time and verify balance
            Wallet::chunk(500, function (Collection $wallets) use ($file, &$inconsistencyFound, &$count) {
                foreach ($wallets as $wallet) {
                    $expected_balance = $wallet->transactions()->sum('amount');
                    if($expected_balance != $wallet->balance){
                        $balance_difference = abs($expected_balance - $wallet->balance);
                        
                        // write transaction details to csv file
                        fputcsv($file, [$wallet->id, $wallet->owner_id, $wallet->owner_type, $expected_balance, $wallet->balance, $balance_difference]);
                        
                         // set inconsistencyFound flag and increment counter
                        if(!$inconsistencyFound) $inconsistencyFound = true;
                        $count++;
                    }
                }
            });
        }catch(\Exception $e){
            echo "Error: " . $e->getMessage();
        }
        
        $result = "Command completed.";
        if($inconsistencyFound) {
            // close the output
            fclose($filePath);

            $result .= " $count inconsistent wallets found. File: $filePath";
        }else{
            // delete created csv file if no inconsistency is found
            unlink($filePath);

            $result .= " No inconsistency found";
        }
        echo $result;
    }
}
