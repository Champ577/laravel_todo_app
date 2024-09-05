<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks,title'
        ]);

        $task = Task::create(['title' => $request->title]);

        return response()->json($task);
    }

    public function toggleCompletion(Task $task)
    {
        $task->is_completed = !$task->is_completed;
        $task->save();

        return response()->json(['status' => 'success']);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['status' => 'success']);
    }

    public function showAllTasks()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }
}
