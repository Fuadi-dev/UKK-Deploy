<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Project Management Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 11px;
                line-height: 1.5;
                color: #1a1a1a;
                background: white;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
                margin-top: 0;
                padding-top: 20px;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 25px;
                font-size: 10px;
            }
            
            th, td {
                border: 1px solid #d0d0d0;
                padding: 10px 12px;
                text-align: left;
            }
            
            th {
                background-color: #f8f9fa;
                font-weight: 600;
                color: #2c3e50;
                text-transform: uppercase;
                font-size: 9px;
                letter-spacing: 0.5px;
            }
            
            tbody tr:nth-child(even) {
                background-color: #fafbfc;
            }
            
            .header {
                text-align: center;
                margin-bottom: 35px;
                border-bottom: 3px solid #4f46e5;
                padding-bottom: 25px;
            }
            
            .header h1 {
                color: #4f46e5;
                font-size: 26px;
                margin-bottom: 12px;
                font-weight: 700;
            }
            
            .header-info {
                color: #64748b;
                font-size: 11px;
                margin-top: 10px;
            }
            
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 35px;
            }
            
            .stat-card {
                border: 2px solid #e2e8f0;
                padding: 18px;
                border-radius: 8px;
                background: #f8fafc;
            }
            
            .stat-card h3 {
                color: #4f46e5;
                font-size: 14px;
                margin-bottom: 12px;
                font-weight: 600;
                border-bottom: 2px solid #e2e8f0;
                padding-bottom: 8px;
            }
            
            .stat-card p {
                margin: 8px 0;
                color: #334155;
                font-size: 11px;
            }
            
            .stat-card strong {
                color: #1e293b;
                font-weight: 600;
            }
            
            .section-title {
                font-size: 16px;
                font-weight: 700;
                margin: 35px 0 18px 0;
                color: #1e293b;
                border-left: 4px solid #4f46e5;
                padding-left: 12px;
                background: #f8fafc;
                padding: 12px;
                padding-left: 12px;
            }
            
            .summary-badge {
                display: inline-block;
                padding: 4px 10px;
                background: #4f46e5;
                color: white;
                border-radius: 4px;
                font-size: 10px;
                margin-right: 8px;
            }
        }
        
        @media screen {
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 30px 20px;
                min-height: 100vh;
            }
            
            .print-container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                padding: 50px;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }
            
            .print-actions {
                position: fixed;
                top: 30px;
                right: 30px;
                display: flex;
                gap: 12px;
                z-index: 1000;
            }
            
            .print-button, .close-button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 14px 28px;
                border-radius: 12px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .print-button:hover, .close-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            }
            
            .print-button:active, .close-button:active {
                transform: translateY(0);
            }
            
            .close-button {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
            }
            
            .close-button:hover {
                box-shadow: 0 6px 20px rgba(245, 87, 108, 0.6);
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
                font-size: 13px;
            }
            
            th, td {
                border: 1px solid #e2e8f0;
                padding: 12px 15px;
                text-align: left;
            }
            
            th {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 11px;
                letter-spacing: 0.5px;
            }
            
            tbody tr {
                transition: background-color 0.2s ease;
            }
            
            tbody tr:nth-child(even) {
                background-color: #f8fafc;
            }
            
            tbody tr:hover {
                background-color: #f1f5f9;
            }
            
            .header {
                text-align: center;
                margin-bottom: 40px;
                border-bottom: 3px solid #4f46e5;
                padding-bottom: 30px;
            }
            
            .header h1 {
                color: #1e293b;
                font-size: 32px;
                margin-bottom: 15px;
                font-weight: 700;
            }
            
            .header-info {
                color: #64748b;
                font-size: 14px;
                margin-top: 12px;
            }
            
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
                margin-bottom: 40px;
            }
            
            .stat-card {
                border: 2px solid #e2e8f0;
                padding: 25px;
                border-radius: 12px;
                background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }
            
            .stat-card:hover {
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
            }
            
            .stat-card h3 {
                color: #4f46e5;
                font-size: 18px;
                margin-bottom: 15px;
                font-weight: 600;
                border-bottom: 2px solid #e2e8f0;
                padding-bottom: 10px;
            }
            
            .stat-card p {
                margin: 10px 0;
                color: #475569;
                font-size: 14px;
                line-height: 1.6;
            }
            
            .stat-card strong {
                color: #1e293b;
                font-weight: 600;
            }
            
            .section-title {
                font-size: 20px;
                font-weight: 700;
                margin: 40px 0 20px 0;
                color: #1e293b;
                border-left: 5px solid #4f46e5;
                padding-left: 15px;
                background: linear-gradient(90deg, #f8fafc 0%, transparent 100%);
                padding: 15px;
                padding-left: 15px;
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Print Actions -->
    <div class="print-actions no-print">
        <button class="close-button" onclick="window.close()">
            <span>✕</span>
            <span>Close</span>
        </button>
        <button class="print-button" onclick="window.print()">
            <span>🖨️</span>
            <span>Print Report</span>
        </button>
    </div>
    
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>📊 Project Management Report</h1>
            <div class="header-info">
                <p><strong>Generated on:</strong> {{ now()->format('l, F j, Y \a\t g:i A') }}</p>
                @if($dateFrom && $dateTo)
                    <p style="margin-top: 8px;">
                        <span class="summary-badge">Report Period</span>
                        {{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}
                    </p>
                @else
                    <p style="margin-top: 8px;">
                        <span class="summary-badge">All Time</span>
                        Complete Historical Data
                    </p>
                @endif
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="section-title">📈 Executive Summary</div>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>🎯 Project Overview</h3>
                <p><strong>Total Projects:</strong> {{ $data['projectStats']['total_projects'] }}</p>
                <p><strong>Active Projects:</strong> <span style="color: #10b981;">{{ $data['projectStats']['active_projects'] }}</span></p>
                <p><strong>Completed Projects:</strong> <span style="color: #3b82f6;">{{ $data['projectStats']['completed_projects'] }}</span></p>
                <p><strong>Projects in Period:</strong> <span style="color: #8b5cf6;">{{ $data['projectStats']['projects_created_in_period'] }}</span></p>
                <p><strong>Completion Rate:</strong> 
                    @php 
                        $total = $data['projectStats']['total_projects'];
                        $completed = $data['projectStats']['completed_projects'];
                        $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                    @endphp
                    <span style="color: {{ $rate >= 70 ? '#10b981' : ($rate >= 40 ? '#f59e0b' : '#ef4444') }};">{{ $rate }}%</span>
                </p>
            </div>
            
            <div class="stat-card">
                <h3>👥 User Overview</h3>
                <p><strong>Total Users:</strong> {{ $data['userStats']['total_users'] }}</p>
                <p><strong>Working Users:</strong> <span style="color: #3b82f6;">{{ $data['userStats']['working_users'] }}</span></p>
                <p><strong>Free Users:</strong> <span style="color: #10b981;">{{ $data['userStats']['free_users'] }}</span></p>
                <p><strong>Administrators:</strong> <span style="color: #8b5cf6;">{{ $data['userStats']['admin_users'] }}</span></p>
                <p><strong>Utilization Rate:</strong> 
                    @php 
                        $utilization = $data['userStats']['total_users'] > 0 
                            ? round(($data['userStats']['working_users'] / $data['userStats']['total_users']) * 100, 1) 
                            : 0;
                    @endphp
                    <span style="color: {{ $utilization >= 70 ? '#10b981' : ($utilization >= 40 ? '#f59e0b' : '#ef4444') }};">{{ $utilization }}%</span>
                </p>
            </div>
        </div>

        <!-- Project Status Distribution -->
        <div class="section-title">📊 Project Status Distribution</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 40%;">Status</th>
                    <th style="width: 25%;">Count</th>
                    <th style="width: 25%;">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php $total = array_sum($data['statusDistribution']); $index = 1; @endphp
                @foreach($data['statusDistribution'] as $status => $count)
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index++ }}</td>
                        <td>
                            <strong style="
                                @if($status === 'active') color: #10b981;
                                @elseif($status === 'completed') color: #3b82f6;
                                @elseif($status === 'cancelled') color: #ef4444;
                                @elseif($status === 'on_hold') color: #f59e0b;
                                @else color: #8b5cf6;
                                @endif
                            ">{{ ucfirst(str_replace('_', ' ', $status)) }}</strong>
                        </td>
                        <td style="font-weight: 600;">{{ $count }}</td>
                        <td>
                            @php $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0 @endphp
                            <strong style="color: {{ $percentage >= 50 ? '#10b981' : ($percentage >= 25 ? '#f59e0b' : '#64748b') }};">
                                {{ $percentage }}%
                            </strong>
                        </td>
                    </tr>
                @endforeach
                @if($total > 0)
                    <tr style="background: #f1f5f9; font-weight: 600;">
                        <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                        <td><strong>{{ $total }}</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Monthly Trend -->
        <div class="section-title">📅 Monthly Project Creation Trend (Last 6 Months)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 60%;">Month</th>
                    <th style="width: 30%;">Projects Created</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; $totalCreated = 0; @endphp
                @foreach($data['monthlyTrend'] as $month)
                    @php $totalCreated += $month['count']; @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index++ }}</td>
                        <td><strong>{{ $month['month_name'] }}</strong></td>
                        <td style="font-weight: 600; color: {{ $month['count'] > 0 ? '#10b981' : '#94a3b8' }};">
                            {{ $month['count'] }}
                        </td>
                    </tr>
                @endforeach
                <tr style="background: #f1f5f9; font-weight: 600;">
                    <td colspan="2" style="text-align: right;"><strong>Total Projects Created:</strong></td>
                    <td><strong style="color: #4f46e5;">{{ $totalCreated }}</strong></td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td colspan="2" style="text-align: right;"><strong>Average per Month:</strong></td>
                    <td><strong style="color: #8b5cf6;">{{ count($data['monthlyTrend']) > 0 ? round($totalCreated / count($data['monthlyTrend']), 1) : 0 }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Page Break -->
        <div class="page-break"></div>

        <!-- Top Performers -->
        <div class="section-title">🏆 Top Performing Users</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Rank</th>
                    <th style="width: 35%;">Name</th>
                    <th style="width: 20%;">Role</th>
                    <th style="width: 20%;">Completed Projects</th>
                    <th style="width: 15%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['topPerformers'] as $index => $performer)
                    <tr>
                        <td style="text-align: center; font-weight: 700; font-size: 14px; color: 
                            @if($index === 0) #fbbf24
                            @elseif($index === 1) #94a3b8
                            @elseif($index === 2) #fb923c
                            @else #64748b
                            @endif
                        ">
                            @if($index === 0) 🥇
                            @elseif($index === 1) 🥈
                            @elseif($index === 2) 🥉
                            @else {{ $index + 1 }}
                            @endif
                        </td>
                        <td><strong>{{ $performer->name }}</strong></td>
                        <td>
                            <span style="
                                @if($performer->role === 'admin') color: #8b5cf6;
                                @elseif($performer->role === 'leader') color: #3b82f6;
                                @else color: #10b981;
                                @endif
                            ">{{ ucfirst($performer->role) }}</span>
                        </td>
                        <td style="text-align: center; font-weight: 700; font-size: 13px; color: #4f46e5;">
                            {{ $performer->completed_projects }}
                        </td>
                        <td style="text-align: center;">
                            <span style="
                                padding: 4px 8px; 
                                border-radius: 4px; 
                                font-size: 9px; 
                                font-weight: 600;
                                @if($performer->status === 'working') 
                                    background: #dbeafe; color: #1e40af;
                                @else 
                                    background: #dcfce7; color: #166534;
                                @endif
                            ">{{ ucfirst($performer->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 30px;">
                            📭 No performance data available for this period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Overdue Projects -->
        <div class="section-title">⚠️ Overdue Projects</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">Project Name</th>
                    <th style="width: 20%;">Created By</th>
                    <th style="width: 15%;">Deadline</th>
                    <th style="width: 15%;">Days Overdue</th>
                    <th style="width: 15%;">Team Size</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['overdueProjects'] as $index => $project)
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                        <td><strong style="color: #ef4444;">{{ $project->project_name }}</strong></td>
                        <td>{{ $project->user->name }}</td>
                        <td style="text-align: center;">
                            {{ $project->deadline ? $project->deadline->format('M j, Y') : 'No deadline' }}
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            @if($project->deadline)
                                @php $daysOverdue = $project->deadline->diffInDays(now()); @endphp
                                <span style="color: 
                                    @if($daysOverdue > 30) #dc2626
                                    @elseif($daysOverdue > 14) #ea580c
                                    @else #f59e0b
                                    @endif
                                ">{{ $daysOverdue }} days</span>
                            @else
                                <span style="color: #94a3b8;">N/A</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: 600; color: #64748b;">
                            {{ $project->members->count() }} 
                            {{ $project->members->count() === 1 ? 'member' : 'members' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #10b981; padding: 30px;">
                            ✅ Great! No overdue projects at this time
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- User Workload Distribution -->
        <div class="section-title">💼 User Workload Distribution</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">User Name</th>
                    <th style="width: 20%;">Role</th>
                    <th style="width: 15%;">Active Projects</th>
                    <th style="width: 15%;">Workload Status</th>
                    <th style="width: 15%;">Current Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['userWorkload'] as $index => $workload)
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                        <td><strong>{{ $workload['user']->name }}</strong></td>
                        <td>
                            <span style="
                                @if($workload['user']->role === 'admin') color: #8b5cf6;
                                @elseif($workload['user']->role === 'leader') color: #3b82f6;
                                @else color: #10b981;
                                @endif
                            ">{{ ucfirst($workload['user']->role) }}</span>
                        </td>
                        <td style="text-align: center; font-weight: 700; color: #4f46e5;">
                            {{ $workload['active_projects'] }}
                        </td>
                        <td style="text-align: center;">
                            <span style="
                                padding: 4px 10px; 
                                border-radius: 4px; 
                                font-size: 9px; 
                                font-weight: 600;
                                @if($workload['workload_status'] === 'free') 
                                    background: #dcfce7; color: #166534;
                                @elseif($workload['workload_status'] === 'overloaded') 
                                    background: #fee2e2; color: #991b1b;
                                @else 
                                    background: #dbeafe; color: #1e40af;
                                @endif
                            ">{{ ucfirst($workload['workload_status']) }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="
                                padding: 4px 10px; 
                                border-radius: 4px; 
                                font-size: 9px; 
                                font-weight: 600;
                                @if($workload['user']->status === 'working') 
                                    background: #dbeafe; color: #1e40af;
                                @else 
                                    background: #dcfce7; color: #166534;
                                @endif
                            ">{{ ucfirst($workload['user']->status) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Recent Activities -->
        <div class="section-title">📋 Recent Activities (Last 10)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Date & Time</th>
                    <th style="width: 70%;">Activity Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['recentActivities']->take(10) as $index => $activity)
                    <tr>
                        <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                        <td style="white-space: nowrap;">
                            <strong>{{ \Carbon\Carbon::parse($activity['date'])->format('M j, Y') }}</strong><br>
                            <span style="color: #64748b; font-size: 9px;">{{ \Carbon\Carbon::parse($activity['date'])->format('h:i A') }}</span>
                        </td>
                        <td>{{ $activity['description'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8; padding: 30px;">
                            📭 No recent activities recorded
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div style="margin-top: 50px; padding-top: 30px; border-top: 3px solid #e2e8f0; text-align: center;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 12px; margin-bottom: 20px;">
                <p style="font-size: 13px; margin: 0; font-weight: 600;">📊 This report was automatically generated by the Project Management System</p>
                <p style="font-size: 11px; margin: 10px 0 0 0; opacity: 0.9;">For any questions or concerns, please contact your system administrator</p>
            </div>
            <div style="color: #94a3b8; font-size: 10px; margin-top: 15px;">
                <p style="margin: 5px 0;">© {{ date('Y') }} Project Management System. All rights reserved.</p>
                <p style="margin: 5px 0;">Report ID: RPT-{{ now()->format('Ymd-His') }} | Generated by: {{ Auth::user()->name ?? 'System' }}</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads if accessed via print URL
        window.addEventListener('load', function() {
            if (window.location.pathname.includes('/print')) {
                // Delay to ensure everything is loaded
                setTimeout(() => {
                    window.print();
                }, 1500);
            }
        });

        // Close window after print dialog is closed (for most browsers)
        window.addEventListener('afterprint', function() {
            // Optional: Auto close after printing
            // setTimeout(() => window.close(), 500);
        });

        // Keyboard shortcut for print (Ctrl+P)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>
