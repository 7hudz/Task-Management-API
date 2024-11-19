<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Task;
use App\Notifications\TaskDueNotification;
use App\Jobs\MarkOverdueTasks;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $tasks = Task::where('due_date', '<=', now()->addDay())->get();

        foreach ($tasks as $task) {
            foreach ($task->users as $user) {
                $user->notify(new TaskDueNotification($task));
            }
        }
    })->daily();
    $schedule->job(new MarkOverdueTasks)->daily();
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
