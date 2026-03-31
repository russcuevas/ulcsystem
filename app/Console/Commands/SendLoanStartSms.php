<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendLoanStartSms extends Command
{
    protected $signature = 'sms:loan-start';
    protected $description = 'Send SMS to clients whose loan starts today';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        $loans = DB::table('clients_loans')
            ->join('clients', 'clients_loans.client_id', '=', 'clients.id')
            ->where('clients_loans.loan_from', $today)
            ->where('clients_loans.loan_start_sms_sent', false)
            ->whereNotNull('clients.phone')
            ->where('clients.phone', '!=', '')
            ->select(
                'clients_loans.id as loan_id',
                'clients.fullname',
                'clients.phone',
                'clients_loans.loan_from'
            )
            ->get();

        $sent = 0;

        foreach ($loans as $loan) {
            $loanDate = Carbon::parse($loan->loan_from)->format('Y-m-d');
            $message = "Magandang araw {$loan->fullname}! Ngayong araw ay simula ng iyong loan ({$loanDate}). Makakatanggap ka ng araw-araw na paalala na payment. Salamat po!";

            try {
                $ch = curl_init();
                $parameters = [
                    'apikey' => env('SEMAPHORE_API_KEY', 'b2a42d09e5cd42585fcc90bf1eeff24e'),
                    'number' => $loan->phone,
                    'message' => $message,
                    'sendername' => env('SEMAPHORE_SENDER_NAME', 'BPTOCEANUS'),
                ];
                curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                DB::table('clients_loans')
                    ->where('id', $loan->loan_id)
                    ->update(['loan_start_sms_sent' => true]);

                $sent++;
                $this->info("SMS sent to {$loan->fullname} ({$loan->phone})");
            } catch (\Exception $e) {
                $this->error("Failed to send SMS to {$loan->fullname}: {$e->getMessage()}");
            }
        }

        $this->info("Done. Sent {$sent} SMS out of {$loans->count()} loans starting today.");

        return self::SUCCESS;
    }
}
