<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - Simple To Do List App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h3 class="text-center">PHP - Simple To Do List App</h3>

    <div class="task-input">
        <input type="text" id="taskInput" class="form-control" placeholder="Enter a new task">
        <button id="addTaskBtn" class="btn btn-primary">Add Task</button>
    </div>

    <ul id="taskList" class="list-group task-list mt-3">
        @foreach ($tasks as $task)
            <li class="list-group-item" data-id="{{ $task->id }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <input type="checkbox" class="taskCheckbox" data-id="{{ $task->id }}" @if($task->is_completed) checked @endif>
                        <span @if($task->is_completed) style="text-decoration: line-through;" @endif>{{ $task->title }}</span>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm editTaskBtn">&#9998;</button>
                        <button class="btn btn-danger btn-sm deleteTaskBtn" data-id="{{ $task->id }}">&#10060;</button>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    <button id="showAllTasksBtn" class="btn btn-secondary mt-3">Show All Tasks</button>
</div>

<script>
    $(document).ready(function() {
        // Add Task without page reload
        $('#addTaskBtn').click(function() {
            let taskTitle = $('#taskInput').val().trim();
            if (taskTitle === '') return;

            $.ajax({
                url: '/tasks',
                type: 'POST',
                data: {
                    title: taskTitle,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#taskList').append(`
                        <li class="list-group-item" data-id="${response.id}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" class="taskCheckbox" data-id="${response.id}">
                                    <span>${response.title}</span>
                                </div>
                                <div>
                                    <button class="btn btn-success btn-sm editTaskBtn">&#9998;</button>
                                    <button class="btn btn-danger btn-sm deleteTaskBtn" data-id="${response.id}">&#10060;</button>
                                </div>
                            </div>
                        </li>
                    `);
                    $('#taskInput').val(''); // Clear input field
                }
            });
        });

        // Mark task as completed
        $(document).on('change', '.taskCheckbox', function() {
            let taskId = $(this).data('id');
            let isChecked = $(this).is(':checked');

            $.ajax({
                url: `/tasks/${taskId}/toggle`,
                type: 'PATCH',
                data: {
                    is_completed: isChecked,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (isChecked) {
                        $(`li[data-id="${taskId}"] span`).css('text-decoration', 'line-through');
                    } else {
                        $(`li[data-id="${taskId}"] span`).css('text-decoration', 'none');
                    }
                }
            });
        });

        // Delete Task with confirmation
        $(document).on('click', '.deleteTaskBtn', function() {
            if (confirm('Are you sure to delete this task?')) {
                let taskId = $(this).data('id');
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $(`li[data-id="${taskId}"]`).remove();
                    }
                });
            }
        });

        // Show all tasks (completed and non-completed)
        $('#showAllTasksBtn').click(function() {
            $.ajax({
                url: '/tasks/all',
                type: 'GET',
                success: function(response) {
                    $('#taskList').empty(); // Clear current list
                    response.forEach(function(task) {
                        $('#taskList').append(`
                            <li class="list-group-item" data-id="${task.id}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <input type="checkbox" class="taskCheckbox" data-id="${task.id}" ${task.is_completed ? 'checked' : ''}>
                                        <span ${task.is_completed ? 'style="text-decoration: line-through;"' : ''}>${task.title}</span>
                                    </div>
                                    <div>
                                        <button class="btn btn-success btn-sm editTaskBtn">&#9998;</button>
                                        <button class="btn btn-danger btn-sm deleteTaskBtn" data-id="${task.id}">&#10060;</button>
                                    </div>
                                </div>
                            </li>
                        `);
                    });
                }
            });
        });
    });
</script>
</body>
</html>
