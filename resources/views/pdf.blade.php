<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Employee Details</h1>
    <table class="">
        <thead class="">
          <tr>
            <th class="">#</th>
            <th class="">Name</th>
            <th class="">Department</th>
            <th class="">Position</th>
            <th class="">Salary</th>
            <th class="">Attendance</th>
            <th class="">Leaves </th>
            <th class="">Performances</th>
            <th class="">Promotions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach ($employees as $key => $employee)
            <tr>
                <td class="">{{$key+1}}</td>
                <td class="">{{$employee->name}}</td>
                <td class="">{{$employee->department->name}}</td>
                <td class="">{{$employee->position}}</td>
                <td class="">{{$employee->salary->base_salary . ' TK'}}</td>
                <td class="">{{$employee->attendances()->where('status','Present')->count()}}</td>
                <td class="">{{$employee->leaves->count()}}</td>
                <td class="">{{$employee->performances->count()}}</td>
                <td class="">{{$employee->promotions->count()}}</td>
            </tr>
          @endforeach

        </tbody>
      </table>
</body>
</html>
