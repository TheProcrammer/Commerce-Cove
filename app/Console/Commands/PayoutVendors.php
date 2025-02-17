<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use App\Models\Payout;
use Carbon\Carbon;
use App\Models\Order;
use App\Enums\OrderStatusEnum;


// Automates the monthly payout process for vendors by calculating their earnings and transferring funds.
class PayoutVendors extends Command
{
    
    protected $signature = 'payout:vendors'; // It can be executed from the terminal using: payout:vendors

    protected $description = 'Perform vendors payout';

    public function handle()
    {
        $this->info('Starting monthly payout process for vendors....');

        // Get all vendors eligible for payout
        $vendors = Vendor::eligibleForPayout()->get();

        // Loop through each vendor and process their payout
        foreach ($vendors as $vendor) {
            $this->processPayout($vendor);
        }

        $this->info('Monthly payout process completed.');
        return self::SUCCESS; // Command::SUCCESS
    }
    protected function processPayout($vendor){
        $this->info('Processing payout for vendor [ID='.$vendor->user_id.'] - "' . $vendor->store_name.'"');
        try{
            \DB::beginTransaction(); // Start transaction

            // Get last payout date
            $startingFrom = Payout::where('vendor_id', $vendor->user_id)
                ->orderBy('until', 'desc')
                ->value('until');

            $startingFrom = $startingFrom ?: Carbon::make('1970-01-01'); // Default to 1970 if no payout
            $until = Carbon::now()->subMonthNoOverFlow()->startOfMonth(); // Previous month start

            // Calculate vendor earnings
            $vendorSubtotal = Order::query()
                ->where('vendor_user_id', $vendor->user_id)
                ->where('status', OrderStatusEnum::Paid->value) // Only count paid orders
                ->whereBetween('created_at', [$startingFrom, $until]) // Filter by time range
                ->sum('vendor_subtotal');

                if($vendorSubtotal){
                    $this->info('Payout made with amount: ' . $vendorSubtotal);
                    // Record the payout
                    Payout::create([
                        'vendor_id' => $vendor->user_id,
                        'amount' => $vendorSubtotal,
                        'starting_from' => $startingFrom,
                        'until'=>$until
                    ]);
                    // Transfer the amount to the vendor (assuming `transfer` method handles this)
                    $vendor->user->transfer((int)($vendorSubtotal * 100), config('app.currency'));
                } else {
                    $this->info('Nothing to process.');
                }
        } catch (\Exception $e){
            \DB::rollBack(); // Rollback on error
            $this->error($e->getMessage());
        }
    }
}
