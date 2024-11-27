<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <main>
            <div class="header py-3 shadow-lg border-2 border-slate-300">
                <div class="container mx-auto">
                    <div class="flex justify-between px-4">
                        <a href="/" class="font-semibold text-xl text-black">Home</a>
                        <a href="/generate/pdf"  class="bg-blue-500 text-white px-4 py-2 rounded">Generate PDF</a>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="bg-gray-200 rounded-full h-4">
                        <div id="progress-bar" class="bg-green-500 h-4 rounded-full" style="width: 0%"></div>
                    </div>
                    <div id="progress-text" class="mt-2 text-center text-sm">Progress: 0%</div>
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
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Average Attend</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase"> Average Leave </th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase"> Leaves </th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase"> Absents </th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Average Performances</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase">Promotions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($employees as $key => $employee)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{$key+1}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{$employee->name}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{$employee->department_name}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{$employee->position}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{$employee->base_salary . ' TK'}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{$employee->attendance_count}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{$employee->average_present . ' AM'}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{$employee->average_leave . ' PM'}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{$employee->leave_count}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{$employee->absent_count}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{$employee->average_score}}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{$employee->promotion_count}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                      </div>
                </div>
            </div>
        </main>

        <script>
            $(document).ready(function () {
                $('#generate-pdf').on('click', function () {

                    $(this).prop('disabled', true);


                    $.ajax({
                        url: '/generate/pdf',
                        method: 'get',
                        success: function (response) {
                            const jobId = response.job_id;
                            checkProgress(jobId);
                        }
                    });
                });

                function checkProgress(jobId) {
                    const interval = setInterval(function () {
                        $.ajax({
                            url: '/pdf/status/' + jobId, // Your Laravel route for checking the status
                            method: 'GET',
                            success: function (response) {
                                const progress = response.progress;
                                const status = response.status;
                                const filePath = response.file_path;

                                // Update the progress bar
                                $('#progress-bar').css('width', progress + '%');
                                $('#progress-text').text('Progress: ' + progress + '%');

                                if (progress === 100) {
                                    clearInterval(interval);
                                    $('#progress-text').text('PDF Generation Completed!');

                                    // Optionally, provide the user with a download link
                                    if (filePath) {
                                        const downloadLink = $('<a>')
                                            .attr('href', '/storage/' + filePath)
                                            .attr('download', 'employee_report.pdf')
                                            .addClass('text-blue-500 underline mt-4 inline-block')
                                            .text('Download PDF');
                                        $('body').append(downloadLink);
                                    }
                                }
                            }
                        });
                    }, 2000); // Check every 2 seconds
                }
            });
        </script>
    </body>
</html>
