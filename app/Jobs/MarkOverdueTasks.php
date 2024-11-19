<?php
namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class MarkOverdueTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $tasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'completed') 
            ->get();

        foreach ($tasks as $task) {
            $task->status = 'overdue';
            $task->save();
        }
    }
}
