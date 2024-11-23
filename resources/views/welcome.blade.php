<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <main>
            <div class="header py-3 shadow-lg border-2 border-slate-300">
                <div class="container mx-auto">
                    <div class="flex justify-between px-4">
                        <a href="/" class="font-semibold text-xl text-black">Home</a>
                        <a href="/generate/pdf" class="font-semibold text-xl text-black">Generate PDF</a>
                    </div>
                </div>

                <div class="container mx-auto py-3 mt-3">
                    <div class="container mx-auto px-4 py-8">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Monthly Employee Details</h2>
                        <div class="overflow-x-auto">
                          <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg">
                            <thead class="bg-gray-100">
                              <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Department</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Position</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Salary</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Attendance</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Leaves </th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Performances</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Promotions</th>
                              </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                              @foreach ($employees as $key => $employee)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{$key+1}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{$employee->name}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{$employee->department->name}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{$employee->position}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{$employee->salary->base_salary . ' TK'}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{$employee->attendances()->where('status','Present')->count()}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{$employee->leaves->count()}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{$employee->performances->count()}}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{$employee->promotions->count()}}</td>
                                </tr>
                              @endforeach

                            </tbody>
                          </table>
                        </div>
                      </div>
                </div>
            </div>
        </main>
    </body>
</html>
