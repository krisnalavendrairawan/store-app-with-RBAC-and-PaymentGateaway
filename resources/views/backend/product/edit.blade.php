@extends('layouts.backend.index')

@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>
                    <form action="{{ route('product.update', $product->encrypted_id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <x-input-text name="name" label="label.name" placeholder="label.name" type="text"
                            :old="old('name', $product->name)" />
                        <x-input-text name="price" label="label.price" placeholder="label.price" type="number"
                            :old="old('price', $product->price)" />
                        <x-input-text name="quantity" label="label.quantity" placeholder="label.quantity" type="number"
                            :old="old('quantity', $product->quantity)" />

                        <x-form-select name="category_id" :label="__('label.category')" :options="$category->pluck('name', 'id')" :selected="old('category_id', $product->category_id)" />

                        <x-text-area name="description" label="label.description" placeholder="label.description"
                            :old="old('description', $product->description)" />
                        @if ($product->image)
                            <div class="d-flex justify-content-center ">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" width="150">
                            </div>
                        @endif
                        <x-input-text name="image" label="label.image" placeholder="label.image" type="file"
                            :old="old('image', $product->image)" />

                        <x-button-submit :cancelRoute="route('product.index')" />
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
                            window.location.href = "{{ route('product.index') }}";
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

        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        "{{ route('product.index') }}";
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        // Handle error
                    }
                });
            });
        });
    </script>
@endpush
