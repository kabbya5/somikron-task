<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }
        th, td {
            word-wrap: break-word;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Employee Report</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Department</th>
            <th>Base Salary</th>
            <th>Attendance Count</th>
            <th>Absent Count</th>
            <th>Average Present</th>
            <th>Average Leave</th>
            <th>Leave Count</th>
            <th>Average Score</th>
            <th>Promotion Count</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employees as $employee)
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->position }}</td>
                <td>{{ $employee->department_name }}</td>
                <td>{{ $employee->base_salary }}</td>
                <td>{{ $employee->attendance_count }}</td>
                <td>{{ $employee->absent_count }}</td>
                <td>{{ $employee->average_present }}</td>
                <td>{{ $employee->average_leave }}</td>
                <td>{{ $employee->leave_count }}</td>
                <td>{{ $employee->average_score }}</td>
                <td>{{ $employee->promotion_count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
