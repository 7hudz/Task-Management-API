<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Task::all(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,in_progress,completed,overdue',
        ]);

        $task = Task::create($validated);
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        return response()->json($task, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:pending,in_progress,completed,overdue',
        ]);

        $task->update($validated);
        return response()->json($task, 200);
    }


    public function assign(Request $request, Task $task)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validated['email'])->first();
        $task->users()->syncWithoutDetaching($user->id);

        return response()->json(['message' => 'Task assigned successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
    public function filterTasks(Request $request)
    {
        $query = Task::query();
    
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
    
        // Filter by due_date if provided
        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }
    
        // You can add additional filters here if needed (e.g., filtering by title, description)
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
    
        // If the 'from_date' and 'to_date' are provided, filter tasks within that date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('due_date', [$request->from_date, $request->to_date]);
        }
    
        // Execute the query and retrieve the filtered tasks
        $tasks = $query->get();
    
        return response()->json($tasks, 200);
    }
    





    public function getTasks()
    {
        
        $tasks = Task::where('due_date', '<', now()) 
                     ->where('status', '!=', 'completed') // Status is not completed
                     ->get();
    
        return response()->json($tasks);
    }
    
}
