<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use App\Models\BankSchedule;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-transfer';

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
        $today = Carbon::today();

        // Get schedules that are active and not stopped
        $schedules = BankSchedule::where('status', 1) // 1 = active
            ->whereDate('start_date', '<=', $today)
            ->get();

        foreach ($schedules as $schedule) {
            // Check if it's the correct transfer date
            $isDue = false;

            if ($schedule->infinite == 1) {
                // Recurring indefinitely every month on the same day as start_date
                $isDue = $schedule->start_date->day == $today->day;
            } else {
                // Continue until end_date
                if ($today->between($schedule->start_date, $schedule->end_date)) {
                    $isDue = $schedule->start_date->day == $today->day;
                } elseif ($today->gt($schedule->end_date)) {
                    // Mark expired
                    $schedule->status = 2; // stopped
                    $schedule->save();
                    Notification::create([
                        'sent_date' => now(),
                        'message' => 'Bank schedule from ' 
                            . BankAccount::find($schedule->from)->bank_name 
                            . ' to ' . BankAccount::find($schedule->to)->bank_name 
                            . ' has expired as of ' . $schedule->end_date->format('Y-m-d') . '.',
                    ]);
                    continue;
                }
            }

            if ($isDue) {
                DB::beginTransaction();
                try {
                    $transferFromAccount = BankAccount::find($schedule->from);
                    $transferToAccount   = BankAccount::find($schedule->to);

                    $balance = $transferFromAccount->transactions()->where('transaction_type', 'credit')->sum('amount') -
                               $transferFromAccount->transactions()->where('transaction_type', 'debit')->sum('amount');

                    // Balance check
                    if ($balance < $schedule->amount) {
                        $this->error("Insufficient balance in {$transferFromAccount->bank_name}");
                        DB::rollBack();
                        Notification::create([
                            'sent_date' => now(),
                            'occasion_name' => 'Scheduled Transfer Failed',
                            'message' => 'Scheduled transfer of ' 
                                . number_format($schedule->amount, 2) 
                                . ' from ' . $transferFromAccount->bank_name 
                                . ' to ' . $transferToAccount->bank_name 
                                . ' for ' . now()->format('F Y') 
                                . ' failed due to insufficient balance.',
                        ]);
                        continue;
                    }

                    $mesgtoslug = 'Scheduled Transfer' . $transferFromAccount->bank_name . '-to-' . $transferToAccount->bank_name;
                    $toBaseSlug = Str::slug($mesgtoslug);
                    $toSlug = $toBaseSlug;
                    $toCounter = 1;

                    while (BankTransaction::where('slug', $toSlug)->exists()) {
                        $toSlug = $toBaseSlug . '-' . $toCounter++;
                    }

                    $finalslug = $toSlug;

                    // Debit transaction
                    $fromTxn = BankTransaction::create([
                        'transaction_date' => $today,
                        'amount' => $schedule->amount,
                        'description' => $schedule->description 
                            ? $schedule->description . ' (Transfer to ' . $transferToAccount->bank_name . ')' 
                            : 'Transfer to ' . $transferToAccount->bank_name,
                        'bank_account_id' => $schedule->from,
                        'transaction_type' => 'debit',
                        'name' => $mesgtoslug,
                        'slug' => $finalslug,
                        'transfer_to' => $schedule->to,
                        'from' => 'scheduled',
                    ]);

                    // Credit transaction
                    BankTransaction::create([
                        'transaction_date' => $today,
                        'amount' => $schedule->amount,
                        'description' => $schedule->description 
                            ? $schedule->description . ' (Transfer from ' . $transferFromAccount->bank_name . ')' 
                            : 'Transfer from ' . $transferFromAccount->bank_name,
                        'bank_account_id' => $schedule->to,
                        'transaction_type' => 'credit',
                        'name' => $mesgtoslug,
                        'slug' => $finalslug,
                        'transfer_from' => $schedule->from,
                        'from_id' => $fromTxn->id,
                        'from' => 'scheduled',
                    ]);

                    // Notification
                    Notification::create([
                        'sent_date' => now(),
                        'occasion_name' => 'Scheduled Transfer',
                        'message' => 'Scheduled transfer of ' 
                            . number_format($schedule->amount, 2) 
                            . ' from ' . $transferFromAccount->bank_name 
                            . ' to ' . $transferToAccount->bank_name 
                            . ' for ' . now()->format('F Y'),
                    ]);

                    DB::commit();
                    $this->info("Transfer successful: {$transferFromAccount->bank_name} -> {$transferToAccount->bank_name}");
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Transfer failed: " . $e->getMessage());
                }
            }
        }
    }
}
