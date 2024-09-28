@extends('layouts.backend.index')

@section('title', $title)
@section('content')

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <x-input-text name="name" label="label.name" placeholder="label.name" type="text"
                            :old="old('name')" />
                        <x-text-area name="description" label="label.description" placeholder="label.description"
                            :old="old('description')" />

                        <x-button-submit :cancelRoute="route('categories.index')" />
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
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href =
                                    "{{ route('categories.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        showErrorMessage(errors);
                    }
                });
            });
        });
    </script>
@endpush
