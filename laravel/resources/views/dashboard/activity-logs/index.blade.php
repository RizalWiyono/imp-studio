@extends('layouts.app')

@section('title', 'Activity Logs')

{{-- push styles --}}
@push('styles')
    <style>
        .placeholder-skeleton {
            color: transparent !important;
            background: #eee;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }

        .placeholder-skeleton::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: translateX(-100%);
            background: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0));
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            100% {
                transform: translateX(100%);
            }
        }
        
        .metadata-preview pre {
            font-size: 0.8rem;
            max-height: 80px;
            overflow-y: auto;
        }
        
        .metadata-toggle {
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 pb-0 mb-2">Activity Logs</h4>
        <p class="mb-4">
            This page displays all user and system activity logs.
        </p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables table border-top" id="activity-logs-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Target Table</th>
                                    <th>IP Address</th>
                                    <th>Metadata</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.activity-logs.partials.detail')
@endsection

@push('scripts')
    <script>
        $(function() {
            let table = $('#activity-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('activity-log.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'fullname',
                    },
                    {
                        data: 'action',
                        searchable: true
                    },
                    {
                        data: 'description',
                    },
                    {
                        data: 'target_table',
                    },
                    {
                        data: 'ip_address',
                    },
                    {
                        data: 'metadata',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action_buttons',
                        orderable: false, 
                        searchable: false
                    }
                ],
                order: [[6, 'desc']], // Sort by created_at desc by default
                language: {
                    sLengthMenu: '_MENU_',
                    search: 'Search',
                    searchPlaceholder: 'Search..'
                }
            });

            // Toggle metadata detail
            $(document).on('click', '.metadata-toggle', function() {
                let id = $(this).data('id');
                $('#metadata-' + id).collapse('toggle');
                $(this).text(function(i, text) {
                    return text === "Show Details" ? "Hide Details" : "Show Details";
                });
            });

            $(document).on('click', '.btn-detail', function() {
                let logId = $(this).data('id');
                setDetailSkeleton(true);
                let offcanvasDetailEl = document.getElementById('offcanvasDetailLog');
                let offcanvasDetailObj = bootstrap.Offcanvas.getInstance(offcanvasDetailEl);
                if (!offcanvasDetailObj) {
                    offcanvasDetailObj = new bootstrap.Offcanvas(offcanvasDetailEl);
                }
                offcanvasDetailObj.show();

                $.ajax({
                    url: '/dashboard/activity-log/' + logId,
                    type: 'GET',
                    success: function(response) {
                        setDetailSkeleton(false);
                        if (response.success) {
                            let log = response.log;
                            
                            // Populate detail view
                            $('#detail_log_id').text(log.id);
                            $('#detail_log_user').text(log.user ? (log.user.profile ? log.user.profile.first_name + ' ' + log.user.profile.last_name : log.user.username) : 'System');
                            $('#detail_log_action').text(log.action || '—');
                            $('#detail_log_description').text(log.description || '—');
                            $('#detail_log_target_table').text(log.target_table || '—');
                            $('#detail_log_target_id').text(log.target_id || '—');
                            $('#detail_log_ip_address').text(log.ip_address || '—');
                            $('#detail_log_user_agent').text(log.user_agent || '—');
                            $('#detail_log_created_at').text(log.created_at || '—');
                            
                            // Handle metadata
                            if (log.formatted_metadata) {
                                $('#detail_log_metadata').empty();
                                $('#detail_log_metadata').append('<pre class="bg-light p-3 rounded">' + 
                                    JSON.stringify(log.formatted_metadata, null, 2) + '</pre>');
                            } else {
                                $('#detail_log_metadata').html('<span class="text-muted">No metadata</span>');
                            }
                            
                            // Handle target information if available
                            if (log.target_exists) {
                                $('#detail_target_status').html('<span class="badge bg-success">Active</span>');
                                $('#detail_target_link').html('<a href="/dashboard/' + 
                                    log.target_table + '/' + log.target_id + 
                                    '" class="btn btn-sm btn-primary">View Target</a>');
                            } else if (log.target_exists === false) {
                                $('#detail_target_status').html('<span class="badge bg-danger">Deleted</span>');
                                $('#detail_target_link').html('<span class="text-muted">Target no longer exists</span>');
                            } else {
                                $('#detail_target_status').html('<span class="badge bg-secondary">Unknown</span>');
                                $('#detail_target_link').html('');
                            }
                        } else {
                            setDetailError('Failed to load details');
                        }
                    },
                    error: function() {
                        setDetailSkeleton(false);
                        setDetailError('Error loading details');
                    }
                });
            });

            function setDetailSkeleton(isLoading) {
                let fields = [
                    '#detail_log_id',
                    '#detail_log_user',
                    '#detail_log_action',
                    '#detail_log_description',
                    '#detail_log_target_table',
                    '#detail_log_target_id',
                    '#detail_log_ip_address',
                    '#detail_log_user_agent',
                    '#detail_log_created_at',
                    '#detail_log_metadata',
                    '#detail_target_status',
                    '#detail_target_link'
                ];
                
                if (isLoading) {
                    fields.forEach(function(selector) {
                        $(selector).empty().addClass('placeholder-skeleton');
                    });
                } else {
                    fields.forEach(function(selector) {
                        $(selector).removeClass('placeholder-skeleton');
                    });
                }
            }

            function setDetailError(message) {
                $('#detail_log_id').text('Error');
                $('#detail_log_user').text('Error');
                $('#detail_log_action').text('Error');
                $('#detail_log_description').text('Error');
                $('#detail_log_target_table').text('Error');
                $('#detail_log_target_id').text('Error');
                $('#detail_log_ip_address').text('Error');
                $('#detail_log_user_agent').text('Error');
                $('#detail_log_created_at').text('Error');
                $('#detail_log_metadata').html('<div class="alert alert-danger">' + message + '</div>');
                $('#detail_target_status').html('<span class="badge bg-danger">Error</span>');
                $('#detail_target_link').html('');
            }

        });
    </script>
@endpush