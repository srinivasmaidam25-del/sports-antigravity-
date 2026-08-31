<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookingService;

class ReleaseExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release temporary slot reservations whose hold time has expired.';

    /**
     * Execute the console command.
     */
    public function handle(BookingService $bookingService)
    {
        $this->info('Starting release of expired reservations...');
        $count = $bookingService->releaseExpiredReservations();
        $this->info("Successfully released {$count} expired reservations.");
    }
}
