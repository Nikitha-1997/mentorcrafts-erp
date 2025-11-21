<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\CustomerProjectDetail;
use App\Models\Reminder;
use Carbon\Carbon;

class CheckExpiringServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'services:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for upcoming domain/hosting expiries and create reminders automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          $today = Carbon::today();
        $upcoming = CustomerProjectDetail::whereNotNull('expiry_date')
                    ->whereBetween('expiry_date', [$today, $today->copy()->addDays(7)])
                    ->get();

        foreach ($upcoming as $project) {
            Reminder::updateOrCreate(
                [
                    'related_id' => $project->id,
                    'type' => 'project_expiry',
                ],
                [
                    'title' => 'Domain Expiry Reminder',
                    'description' => 'The domain "' . ($project->domain_name ?? 'N/A') . '" for project "' . ($project->project_name ?? 'Unnamed Project') . '" will expire on ' . Carbon::parse($project->expiry_date)->format('d M, Y'),
                    'remind_at' => Carbon::parse($project->expiry_date)->subDays(7),
                ]
            );
        }

        $this->info('Expiry reminders updated successfully.');
    }
}
