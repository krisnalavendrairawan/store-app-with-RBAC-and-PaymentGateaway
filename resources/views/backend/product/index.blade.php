@extends('layouts.backend.index')
@section('title', $title)
@section('content')

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>
                    <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm mb-3">
                        <i class="mdi mdi-plus-box"></i> &nbsp;{{ __('label.create') }}
                    </a>
                    <div class="table-responsive">
                        <table class="table" id="table-product">
                            <thead>
                                <tr>
                                    <th> {{ __('label.no') }} </th>
                                    <th> {{ __('label.name') }} </th>
                                    <th> {{ __('label.price') }} </th>
                                    <th> {{ __('label.quantity') }} </th>
                                    <th> {{ __('label.category') }} </th>
                                    <th> {{ __('label.description') }} </th>
                                    <th> {{ __('label.image') }} </th>
                                    <th> {{ __('label.action') }} </th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#table-product').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('product.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'price',
                        name: 'price',
                        render: function(data, type, row) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                        }
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'image_url',
                        name: 'image_url',
                        render: function(data, type, full, meta) {
                            return data ? "<img src='" + data +
                                "' class='img-fluid' style='max-width: 100px' />" : '';
                        }
                    },

                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            let url_edit = "{{ route('product.edit', ':id') }}";
                            let url_destroy = "{{ route('product.destroy', ':id') }}";

                            url_edit = url_edit.replace(':id', row.encrypted_id);
                            url_destroy = url_destroy.replace(':id', row.encrypted_id);

                            return `
                            <a href="${url_edit}" class="btn btn-warning btn-sm">
                                <i class="mdi mdi-pencil"></i> &nbsp;{{ __('label.edit') }}
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteOption('${url_destroy}')">
                                <i class="mdi mdi-delete"></i> &nbsp;{{ __('label.delete') }}
                            </button>
                            `;
                        }
                    }
                ],
                drawCallback: function(settings) {
                    // Ini akan dipanggil setiap kali tabel di-redraw
                    console.log('Table redrawn');
                }
            });
            window.refreshTable = function() {
                if ($.fn.DataTable.isDataTable('#table-product')) {
                    table.ajax.reload(null, false);
                } else {
                    console.error('DataTable is not initialized');
                }
            };
        });

        @if (session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endpush
