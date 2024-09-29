@extends('layouts.backend.index')

@section('title', $title)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>

                    {{-- User Search with Select2 --}}
                    <div class="mb-4">
                        <label for="user-search" class="form-label">{{ __('label.search_user') }}</label>
                        <select id="user-search" class="form-control select2" style="width: 100%;">
                            <option value="">{{ __('label.select_user') }}</option>
                        </select>
                    </div>

                    {{-- Roles and Permissions Section --}}
                    <div id="roles-permissions-section" class="mt-4 d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Roles</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="roles-list" class="form-check">
                                            <!-- Roles will be dynamically loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Permissions</h5>
                                    </div>
                                    <div class="card-body p-3">
                                        <div id="permissions-list" class="form-check">

                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="{{ route('dashboard.index') }}"
                                        class="btn btn-danger">{{ __('label.cancel') }}</a>
                                    <button id="save-roles-permissions"
                                        class="btn btn-primary">{{ __('label.save_changes') }}
                                    </button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendors/select2/css/select2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('vendors/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#user-search').select2({
                placeholder: '-- Select User --',
                ajax: {
                    url: "{{ route('users.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term // Search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(user) {
                                return {
                                    id: user.id,
                                    text: user.name + ' (' + user.email + ')'
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#user-search').on('select2:select', function(e) {
                selectedUserId = e.params.data.id;
                let userName = e.params.data.text;

                $.ajax({
                    url: '/role/get-roles-permissions/' + selectedUserId,
                    success: function(data) {
                        $('#roles-permissions-section').removeClass('d-none');

                        // Populate roles checkboxes
                        $('#roles-list').empty();
                        data.roles.forEach(function(role) {
                            let checked = data.userRoles.includes(role.name) ?
                                'checked' : '';
                            $('#roles-list').append(`
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="${role.name}" ${checked}>
                        <label class="form-check-label">${role.name}</label>
                    </div>
                `);
                        });

                        console.log('Full data received:', data);
                        console.log('User Permissions:', data.userPermissions);

                        $('#permissions-list').empty();
                        data.permissions.forEach(function(permission) {
                            let checked = data.userPermissions.includes(permission
                                .name) ? 'checked' : '';

                            console.log('Permission:', permission.name);
                            console.log('Included in user permissions:', data
                                .userPermissions.includes(permission.name));
                            console.log('Checked status:', checked);

                            $('#permissions-list').append(`
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="${permission.name}" ${checked}>
                                    <label class="form-check-label">${permission.name}</label>
                                </div>
                            `);
                        });
                    }
                });
            });

            $('#save-roles-permissions').on('click', function() {
                var roles = [];
                var permissions = [];

                $('input[name="roles[]"]:checked').each(function() {
                    roles.push($(this).val());
                });

                $('input[name="permissions[]"]:checked').each(function() {
                    permissions.push($(this).val());
                });

                $.ajax({
                    url: `/role/assign-roles-permissions/${$('#user-search').val()}`,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        roles: roles,
                        permissions: permissions
                    },
                    success: function(response) {
                        alert('Roles and permissions updated successfully.');
                    },
                    error: function() {
                        alert('Error updating roles and permissions.');
                    }
                });
            });
        });
    </script>
@endpush
