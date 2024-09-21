<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MarkInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:mark-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark users as inactive based on last_active_at';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $threshold = now()->subMinutes(5);
        User::where('last_active_at', '<', $threshold)
            ->update(['is_active' => false]);

        $this->info('Inactive users marked successfully.');
    }
}
