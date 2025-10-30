{{-- dashboard.system-settings.index.php --}}
@extends('layouts.app')

@section('title', 'System Settings')

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
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 pb-0 mb-2">System Settings</h4>
        <p class="mb-4">
            This page is used to manage system settings. You can add, edit, and delete system settings here.
        </p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            @include('dashboard.system-settings.partials.widget')
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables table border-top" id="system-settings-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.system-settings.partials.create')
    @include('dashboard.system-settings.partials.edit')
    @include('dashboard.system-settings.partials.detail')

@endsection

@push('scripts')
    <script>
        // Function to format JSON string to pretty format with proper indentation
  function formatJSONValue(value) {
    try {
      // Attempt to parse the string as JSON
      const parsedJSON = JSON.parse(value);
      // Return the JSON string with pretty formatting (4 spaces indentation)
      return JSON.stringify(parsedJSON, null, 4);
    } catch (e) {
      // If parsing fails, it's not valid JSON, return the original value
      return value;
    }
  }

  // Function to validate JSON without formatting
  function isValidJSON(value) {
    try {
      JSON.parse(value);
      return true;
    } catch (e) {
      return false;
    }
  }
        $(function() {
            let table = $('#system-settings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('settings.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'key'
                    },
                    {
                        data: 'value'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    sLengthMenu: '_MENU_',
                    search: 'Search',
                    searchPlaceholder: 'Search..'
                }
            });

            $(document).on('click', '.btn-edit', function() {
                let settingId = $(this).data('id');
                let key = $(this).data('key');
                let value = $(this).data('value');
                let type = $(this).data('type');
                let description = $(this).data('description') || '';
                let status = $(this).data('status');
                if (type === 'JSON' && typeof value === 'object') {
                    value = JSON.stringify(value);
                }
                $('#system_setting_id').val(settingId);
                $('#edit_key').val(key);
                $('#edit_value').val(value);
                $('#edit_type').val(type).trigger('change');
                $('#edit_description').val(description);
                $('#edit_status').val(status).trigger('change');
                
                // Set nilai berdasarkan type setelah container sesuai ditampilkan
                setTimeout(function() {
                    switch(type) {
                        case 'STRING':
                            $('#edit-value-string').val(value);
                            break;
                        case 'INTEGER':
                            $('#edit-value-integer').val(value);
                            break;
                        case 'BOOLEAN':
                        if (value == 1) {
                            $('#edit-value-boolean-true').prop('checked', true);
                            } else {
                            $('#edit-value-boolean-false').prop('checked', true);
                            }
                            break;
                        case 'JSON':
                        $('#edit-value-json').val(formatJSONValue(value));
                            break;
                        case 'URL':
                            $('#edit-value-url').val(value);
                            break;
                    }
                }, 100);
                let offcanvasEl = document.getElementById('offcanvasEditSystemSetting');
                let offcanvasObj = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (!offcanvasObj) {
                    offcanvasObj = new bootstrap.Offcanvas(offcanvasEl);
                }
                offcanvasObj.show();
            });

            $(document).on('click', '.btn-detail', function() {
                let settingId = $(this).data('id');
                setDetailSkeleton(true);
                let offcanvasDetailEl = document.getElementById('offcanvasDetailSystemSetting');
                let offcanvasDetailObj = bootstrap.Offcanvas.getInstance(offcanvasDetailEl);
                if (!offcanvasDetailObj) {
                    offcanvasDetailObj = new bootstrap.Offcanvas(offcanvasDetailEl);
                }
                offcanvasDetailObj.show();

                $.ajax({
                    url: '/dashboard/settings/' + settingId,
                    type: 'GET',
                    success: function(response) {
                        setDetailSkeleton(false);
                        if (response.success) {
                            let setting = response.setting;

                            $('#detail_setting_id').text(setting.id);
                            $('#detail_setting_key').text(setting.key);
                            if (setting.type === 'BOOLEAN') {
                                // For boolean type, display True or False
                                const boolValue = setting.value == 1 ? 'True' : 'False';
                                $('#detail_setting_value').text(boolValue);
                            } else if (setting.type === 'JSON') {
                                // For JSON type, try to pretty print
                                try {
                                    const jsonObj = JSON.parse(setting.value);
                                    const prettyJson = JSON.stringify(jsonObj, null, 2);
                                    $('#detail_setting_value').html('<pre style="margin: 0; white-space: pre-wrap;">' + 
                                                                prettyJson + '</pre>');
                                } catch (e) {
                                    // If not valid JSON, show as is
                                    $('#detail_setting_value').text(setting.value);
                                }
                            } else {
                                // For other types, display as is
                                $('#detail_setting_value').text(setting.value);
                            }
                            $('#detail_setting_description').html(setting.description || 'No description');
                            
                            // Format creator and updater
                            let createdBy = 'Unknown';
                            if (setting.creator) {
                                createdBy = setting.creator.username || 'Unknown';
                            }
                            
                            let updatedBy = 'Unknown';
                            if (setting.updater) {
                                updatedBy = setting.updater.username || 'Unknown';
                            }
                            
                            $('#detail_setting_created_by').text(createdBy);
                            $('#detail_setting_updated_by').text(updatedBy);
                            $('#detail_setting_created_at').text(formatDateTime(setting.created_at));
                            $('#detail_setting_updated_at').text(formatDateTime(setting.updated_at));

                            // Set type with styling
                            let typeColors = {
                                'STRING': 'primary',
                                'INTEGER': 'info',
                                'BOOLEAN': 'success',
                                'JSON': 'warning',
                                'URL': 'dark',
                            };
                            
                            let typeColor = typeColors[setting.type] || 'secondary';
                            
                            $('#detail_setting_type')
                                .removeClass()
                                .addClass('badge bg-' + typeColor)
                                .text(setting.type);

                            // Set status with styling
                            let statusColors = {
                                'ACTIVE': 'success',
                                'INACTIVE': 'secondary',
                                'ARCHIVED': 'info',
                                'DELETED': 'dark',
                            };

                            let statusLabels = {
                                'ACTIVE': 'Active',
                                'INACTIVE': 'Inactive',
                                'ARCHIVED': 'Archived',
                                'DELETED': 'Deleted',
                            };

                            let statusColor = statusColors[setting.status] || 'secondary';
                            let statusLabel = statusLabels[setting.status] || 'Unknown';

                            $('#detail_setting_status')
                                .removeClass()
                                .addClass('badge bg-' + statusColor)
                                .text(statusLabel);
                        } else {
                            $('#detail_setting_id').text('Error');
                            $('#detail_setting_key').text('Error');
                            $('#detail_setting_value').text('Error');
                            $('#detail_setting_description').text('Error');
                            $('#detail_setting_type')
                                .removeClass()
                                .addClass('badge bg-secondary')
                                .text('Error');
                            $('#detail_setting_status')
                                .removeClass()
                                .addClass('badge bg-secondary')
                                .text('Error');
                            $('#detail_setting_created_by').text('Error');
                            $('#detail_setting_updated_by').text('Error');
                            $('#detail_setting_created_at').text('Error');
                            $('#detail_setting_updated_at').text('Error');
                        }
                    },
                    error: function() {
                        setDetailSkeleton(false);
                        $('#detail_setting_id').text('Error');
                        $('#detail_setting_key').text('Error');
                        $('#detail_setting_value').text('Error');
                        $('#detail_setting_description').text('Error');
                        $('#detail_setting_type')
                            .removeClass()
                            .addClass('badge bg-secondary')
                            .text('Error');
                        $('#detail_setting_status')
                            .removeClass()
                            .addClass('badge bg-secondary')
                            .text('Error');
                        $('#detail_setting_created_by').text('Error');
                        $('#detail_setting_updated_by').text('Error');
                        $('#detail_setting_created_at').text('Error');
                        $('#detail_setting_updated_at').text('Error');
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let settingId = $(this).data('id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.showLoading();
                        
                        // Membuat form untuk mengirim DELETE request dengan benar
                        let form = $('<form>', {
                            'method': 'POST',
                            'action': '/dashboard/settings/' + settingId
                        });
                        
                        // Tambahkan CSRF token
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': '{{ csrf_token() }}'
                        }));
                        
                        // Tambahkan method spoofing untuk DELETE
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_method',
                            'value': 'DELETE'
                        }));
                        
                        // Tambahkan form ke body, submit, lalu hapus
                        $('body').append(form);
                        
                        // Kirim melalui AJAX alih-alih submit form
                        $.ajax({
                            url: '/dashboard/settings/' + settingId,
                            type: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                form.remove();
                                
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'System setting has been deleted.'
                                });
                                
                                // Refresh datatable
                                table.ajax.reload();
                                updateWidgetStats();
                            },
                            error: function(xhr) {
                                form.remove();
                                
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message || 'An error occurred while deleting.'
                                });
                                
                            }
                        });
                    }
                });
            });          

            $('#addNewSystemSettingForm').validate({
                rules: {
                    key: {
                        required: true,
                        minlength: 3
                    },
                    value: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    key: {
                        required: 'Key must be filled',
                        minlength: 'Key must be at least 3 characters'
                    },
                    value: {
                        required: 'Value must be filled'
                    },
                    type: {
                        required: 'Type must be selected'
                    },
                    status: {
                        required: 'Status must be selected'
                    }
                },
                submitHandler: function(form) {
                    Swal.showLoading();
                    $.ajax({
                        url: '{{ route('settings.store') }}',
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            table.ajax.reload();
                            let offcanvasAdd = bootstrap.Offcanvas.getInstance(document
                                .getElementById('offcanvasAddSystemSetting'));
                            if (offcanvasAdd) {
                                offcanvasAdd.hide();
                            }
                            form.reset();
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                            updateWidgetStats();
                        },
                        error: function(xhr) {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message
                            });
                        }
                    });
                    return false;
                }
            });

            $('#editSystemSettingForm').validate({
                rules: {
                    key: {
                        required: true,
                        minlength: 3
                    },
                    value: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    key: {
                        required: 'Key must be filled',
                        minlength: 'Key must be at least 3 characters'
                    },
                    value: {
                        required: 'Value must be filled'
                    },
                    type: {
                        required: 'Type must be selected'
                    },
                    status: {
                        required: 'Status must be selected'
                    }
                },
                submitHandler: function(form) {
                    // Ambil nilai dari input yang sesuai berdasarkan tipe
                    let settingId = $('#system_setting_id').val();
                    let selectedType = $('#edit_type').val();
                    let valueToSubmit;
                    
                    // Get the value from the appropriate input based on type
                    switch(selectedType) {
                        case 'STRING':
                            valueToSubmit = $('#edit-value-string').val();
                            break;
                        case 'INTEGER':
                            valueToSubmit = $('#edit-value-integer').val();
                            break;
                        case 'BOOLEAN':
                            valueToSubmit = $('input[name="edit_value"]:checked').val() || "0"; // Default to "0" if none selected
                            break;
                        case 'JSON':
                            valueToSubmit = $('#edit-value-json').val();
                            break;
                        case 'URL':
                            valueToSubmit = $('#edit-value-url').val();
                            break;
                    }
                    
                    // Debugging
                    
                    // Buat objek FormData untuk menambahkan value yang diambil secara manual
                    var formData = new FormData(form);
                    formData.append('value', valueToSubmit);
                    formData.append('_method', 'PUT'); // Penting untuk method spoofing di Laravel
                    
                    Swal.showLoading();
                    
                    $.ajax({
                        url: '/dashboard/settings/' + settingId,
                        type: 'POST', // Tetap gunakan POST, tapi dengan _method: PUT
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // Handle success...
                            
                            let offcanvasEditEl = document.getElementById('offcanvasEditSystemSetting');
                            let offcanvasEditObj = bootstrap.Offcanvas.getInstance(offcanvasEditEl);
                            if (!offcanvasEditObj) {
                                offcanvasEditObj = new bootstrap.Offcanvas(offcanvasEditEl);
                            }
                            offcanvasEditObj.hide();
                            
                            table.ajax.reload();
                            form.reset();
                            
                            Swal.close();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                            
                            updateWidgetStats();
                        },
                        error: function(xhr) {
                            console.error("Update error:", xhr);
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Unknown error occurred'
                            });
                        }
                    });
                    
                    return false;
                }
            });
            function setDetailSkeleton(isLoading) {
                let fields = [
                    '#detail_setting_id',
                    '#detail_setting_key',
                    '#detail_setting_value',
                    '#detail_setting_description',
                    '#detail_setting_type',
                    '#detail_setting_status',
                    '#detail_setting_created_by',
                    '#detail_setting_updated_by',
                    '#detail_setting_created_at',
                    '#detail_setting_updated_at'
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

            function formatDateTime(dateString) {
                if (!dateString) return 'Unknown';
                
                const options = { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                
                return new Date(dateString).toLocaleDateString('en-US', options);
            }
        });
        
        $(document).on('click', '[data-bs-target="#offcanvasAddSystemSetting"]', function() {
            $('#addNewSystemSettingForm')[0].reset();
            $('#type').val('').trigger('change');
            $('#status').val('').trigger('change');
        });

        // Fungsi untuk memperbarui widget statistik
        function updateWidgetStats() {
            $.ajax({
                url: window.location.pathname + '?get_stats=true',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Update nilai statistik pada widget
                    if (response.stats) {
                        if (response.stats.totalSettingActive !== undefined) {
                            $('.setting-active-count').text(response.stats.totalSettingActive);
                        }
                        if (response.stats.totalSettingInactive !== undefined) {
                            $('.setting-inactive-count').text(response.stats.totalSettingInactive);
                        }
                    }
                }
            });
        }
    </script>
@endpush