Laravel Task Management API

Description

A RESTful API built with Laravel to manage tasks with features like:


Task CRUD operations.

User assignment to tasks.

Notifications for tasks due within 24 hours.

Task filtering and overdue handling.

Features

Task Management: Create, update, delete, and retrieve tasks.

User Assignment: Assign tasks to users via email.

Notifications: Notify users of tasks due in 24 hours using Laravel notifications.

Bonus Features:

Task filtering by status or due date.

Overdue task detection and marking.




bash

git clone https://github.com/7hudz/Task-Management-API.git


Install dependencies:

bash

composer install

Copy the .env.example file and configure:

bash

cp .env.example .env


Generate the application key:

bash
php artisan key:generate

Set up the database:

Create a database (e.g., tasks_app).
Update .env with database credentials.
Run migrations and seed data:

bash

php artisan migrate 
Start the development server:


php artisan serve



API Documentation
Import the Postman collection located in https://www.postman.com/mousa1998/task-management-api/collection/3m48au0/task-management-api?action=share&creator=36250666.



To test notifications:

by tinker in terminal in vs code 
and should the real email
php artisan tinker

$task = \App\Models\Task::find(2); // Ensure this ID exists in the database
$user = \App\Models\User::find(13); // Ensure this ID exists in the database
$user->notify(new \App\Notifications\TaskDueNotification($task));


Adjust the Kernel.php schedule for testing.
Adjust Kernel for Testing
In app/Console/Kernel.php, change the scheduler frequency from daily to every minute for testing:

php

protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $tasks = Task::where('due_date', '<=', now()->addDay())->get();

        foreach ($tasks as $task) {
            foreach ($task->users as $user) {
                $user->notify(new TaskDueNotification($task));
            }
        }
    })->everyMinute();
}
After testing, revert it to:


$schedule->call(...)->daily();
First Steps for Others to Use Your Project




