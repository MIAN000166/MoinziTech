<?php

namespace App\Console;

use App\Models\Admin\CaseForward;
use App\Models\Hospital\Report;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
//

            $adminID=User::where('role_id',1)->first();

        $pendingReports = Report::where('approval', 'pending')
            ->where('forwarded', false)
            ->where('report',null)
            ->where('auto_assign',1)

            ->get();


        $activeRadiologists = User::where('role_id', 3)
            ->where('status', 'active')
            ->where('user_verified', 1)
            ->get();

        if (count($pendingReports)>0 && count($activeRadiologists)>0){


        $pendingReportsCount = count($pendingReports);
        $radiologistIndex = 0;
        foreach ($pendingReports as $report) {

            $radiologist = $activeRadiologists[$radiologistIndex];


            CaseForward::create([
                'hospital_id' => $report->hospital_id,
                'report_id' => $report->id,
                'radiologist_id' => $radiologist->id,
                'user_id'=>$adminID->id,
            ]);

            $report->update(['forwarded' => 1,'approval'=>"approved"]);

            // Move to the next radiologist in a round-robin fashion
            $radiologistIndex = ($radiologistIndex + 1) % count($activeRadiologists);
        }
        }

        });


    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
