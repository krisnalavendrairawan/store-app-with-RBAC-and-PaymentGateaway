@extends('layouts.backend.index')

@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>
                    <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-input-text name="name" label="label.name" placeholder="label.name" type="text"
                            :old="old('name')" />
                        <x-input-text name="price" label="label.price" placeholder="label.price" type="number"
                            :old="old('price')" />
                        <x-input-text name="quantity" label="label.quantity" placeholder="label.quantity" type="number"
                            :old="old('quantity')" />

                        <x-form-select name="category_id" :label="__('label.category')" :options="$category->pluck('name', 'id')" :selected="old('category_id')" />

                        <x-text-area name="description" label="label.description" placeholder="label.description"
                            :old="old('description')" />
                        <x-input-text name="image" label="label.image" placeholder="label.image" type="file"
                            :old="old('image')" />

                        <x-button-submit :cancelRoute="route('product.index')" />
                    </form>
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
            $('.select2').select2({
                theme: 'bootstrap-5',
            });
        });
    </script>
@endpush
