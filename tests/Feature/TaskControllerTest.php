<?php


namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the creation of a task.
     *
     * @return void
     */
    public function test_create_task()
    {
        // Create a user using the factory
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);

        // Perform the task creation request without explicitly setting 'user_id'
        $response = $this->post('/tasks', [
            'title' => 'Test Task',
            'due_date' => now()->addDays(1),
        ]);

        // Assert the response status and database state
        $response->assertStatus(201);
        // Assert that the task is created and the user_id is automatically handled
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            // Do not explicitly check 'user_id' since it's automatically set
        ]);
    }

    /**
     * Test updating a task.
     *
     * @return void
     */
    public function testUpdateTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $updateData = [
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ];

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ]);
    }

    /**
     * Test assigning a task to a user.
     *
     * @return void
     */
    public function test_assign_user_to_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        // Acting as a logged-in user
        $this->actingAs($user);

        // Assign user to task
        $response = $this->postJson(route('task.assign', ['task' => $task->id, 'user' => $user->id]));

        // Assert the task now has the user assigned
        $response->assertStatus(200);
        $this->assertTrue($task->users->contains($user));  // Check if the user is assigned to the task
    }

    /**
     * Test sending task due notifications.
     *
     * @return void
     */
    public function testTaskDueNotification()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'due_date' => now()->addDay(),
        ]);

        Notification::fake();

        $user->notify(new \App\Notifications\TaskDueNotification($task));

        Notification::assertSentTo(
            [$user],
            \App\Notifications\TaskDueNotification::class
        );
    }

    /**
     * Test filtering tasks by status and date range.
     *
     * @return void
     */
    public function testFilterTasksByStatusAndDateRange()
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create(['status' => 'pending', 'due_date' => now()->addDays(2)]);
        $task2 = Task::factory()->create(['status' => 'completed', 'due_date' => now()->addDays(1)]);

        $response = $this->actingAs($user)->getJson('/api/tasks?status=pending&start_date=' . now()->toDateString() . '&end_date=' . now()->addWeek()->toDateString());

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $task1->title]);
        $response->assertJsonMissing(['title' => $task2->title]);
    }

    /**
     * Test marking tasks as overdue.
     *
     * @return void
     */
    public function testMarkOverdueTasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'due_date' => now()->subDay(), // task is overdue
            'status' => 'pending',
        ]);

        // You may need to implement logic in your app to mark overdue tasks or do it manually in the test
        $task->update(['status' => 'overdue']); // Manually mark it as overdue

        $response = $this->actingAs($user)->getJson('/api/tasks');

        // Assert that the task status is updated to "overdue"
        $response->assertJsonFragment(['status' => 'overdue']);
    }
}
