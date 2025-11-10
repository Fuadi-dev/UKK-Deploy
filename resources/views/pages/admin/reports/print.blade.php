<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Project Management Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 2cm;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #333;
                background: white;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            
            th {
                background-color: #f5f5f5;
                font-weight: bold;
            }
            
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
            }
            
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .stat-card {
                border: 1px solid #ddd;
                padding: 15px;
                border-radius: 5px;
            }
            
            .section-title {
                font-size: 16px;
                font-weight: bold;
                margin: 30px 0 15px 0;
                color: #333;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
            }
        }
        
        @media screen {
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #f8f9fa;
                margin: 0;
                padding: 20px;
            }
            
            .print-container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            }
            
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #007bff;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
            }
            
            .print-button:hover {
                background: #0056b3;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Report</button>
    
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>Project Management Report</h1>
            <p><strong>Generated on:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
            @if($dateFrom && $dateTo)
                <p><strong>Report Period:</strong> {{ $dateFrom }} to {{ $dateTo }}</p>
            @endif
        </div>

        <!-- Executive Summary -->
        <div class="section-title">Executive Summary</div>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Project Overview</h3>
                <p><strong>Total Projects:</strong> {{ $data['projectStats']['total_projects'] }}</p>
                <p><strong>Active Projects:</strong> {{ $data['projectStats']['active_projects'] }}</p>
                <p><strong>Completed Projects:</strong> {{ $data['projectStats']['completed_projects'] }}</p>
                <p><strong>Projects in Period:</strong> {{ $data['projectStats']['projects_created_in_period'] }}</p>
            </div>
            
            <div class="stat-card">
                <h3>User Overview</h3>
                <p><strong>Total Users:</strong> {{ $data['userStats']['total_users'] }}</p>
                <p><strong>Working Users:</strong> {{ $data['userStats']['working_users'] }}</p>
                <p><strong>Free Users:</strong> {{ $data['userStats']['free_users'] }}</p>
                <p><strong>Admins:</strong> {{ $data['userStats']['admin_users'] }}</p>
            </div>
        </div>

        <!-- Project Status Distribution -->
        <div class="section-title">Project Status Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php $total = array_sum($data['statusDistribution']) @endphp
                @foreach($data['statusDistribution'] as $status => $count)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                        <td>{{ $count }}</td>
                        <td>{{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Monthly Trend -->
        <div class="section-title">Monthly Project Creation Trend (Last 6 Months)</div>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Projects Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['monthlyTrend'] as $month)
                    <tr>
                        <td>{{ $month['month_name'] }}</td>
                        <td>{{ $month['count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Page Break -->
        <div class="page-break"></div>

        <!-- Top Performers -->
        <div class="section-title">Top Performing Users</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Completed Projects</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['topPerformers'] as $performer)
                    <tr>
                        <td>{{ $performer->name }}</td>
                        <td>{{ ucfirst($performer->role) }}</td>
                        <td>{{ $performer->completed_projects }}</td>
                        <td>{{ ucfirst($performer->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #666;">No data available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Overdue Projects -->
        <div class="section-title">Overdue Projects</div>
        <table>
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Created By</th>
                    <th>Deadline</th>
                    <th>Days Overdue</th>
                    <th>Members</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['overdueProjects'] as $project)
                    <tr>
                        <td>{{ $project->project_name }}</td>
                        <td>{{ $project->user->name }}</td>
                        <td>{{ $project->deadline ? $project->deadline->format('Y-m-d') : 'No deadline' }}</td>
                        <td>
                            @if($project->deadline)
                                {{ $project->deadline->diffInDays(now()) }} days
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $project->members->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #666;">No overdue projects</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- User Workload Distribution -->
        <div class="section-title">User Workload Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Role</th>
                    <th>Active Projects</th>
                    <th>Workload Status</th>
                    <th>Current Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['userWorkload'] as $workload)
                    <tr>
                        <td>{{ $workload['user']->name }}</td>
                        <td>{{ ucfirst($workload['user']->role) }}</td>
                        <td>{{ $workload['active_projects'] }}</td>
                        <td>{{ ucfirst($workload['workload_status']) }}</td>
                        <td>{{ ucfirst($workload['user']->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Recent Activities -->
        <div class="section-title">Recent Activities (Last 10)</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['recentActivities']->take(10) as $activity)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($activity['date'])->format('Y-m-d H:i') }}</td>
                        <td>{{ $activity['description'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; color: #666;">No recent activities</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 20px;">
            <p>This report was automatically generated by the Project Management System</p>
            <p>© {{ date('Y') }} Project Management System. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads if accessed via print URL
        window.addEventListener('load', function() {
            if (window.location.pathname.includes('/print')) {
                setTimeout(() => {
                    window.print();
                }, 1000);
            }
        });
    </script>
</body>
</html>
