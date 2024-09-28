@extends('layouts.backend.index')

@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>
                    <form action="{{ route('user.update', $user->encrypted_id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <x-input-text name="name" label="label.name" placeholder="label.name" type="text"
                            :old="old('name', $user->name)" />
                        <x-input-text name="email" label="label.email" placeholder="label.email" type="email"
                            :old="old('email', $user->email)" />
                        <x-input-text name="phone" label="label.phone_number" placeholder="label.phone_number"
                            type="number" :old="old('phone', $user->phone)" />
                        <x-button-submit :cancelRoute="route('user.index')" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/notif.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = "{{ route('user.index') }}";
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endpush
