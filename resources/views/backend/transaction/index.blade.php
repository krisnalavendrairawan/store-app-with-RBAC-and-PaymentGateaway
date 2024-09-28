@extends('layouts.backend.index')
@section('title', $title)
@section('content')

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $title }}</h4>
                    <a href="{{ route('transaction.create') }}" class="btn btn-primary btn-sm mb-3">
                        <i class="mdi mdi-plus-box"></i> &nbsp;{{ __('label.create') }}
                    </a>
                    <div class="table-responsive">
                        <table class="table" id="table-transaction">
                            <thead>
                                <tr>
                                    <th> {{ __('label.no') }} </th>
                                    <th> {{ __('label.name') }} </th>
                                    <th> {{ __('label.product') }} </th>
                                    <th> {{ __('label.amount') }} </th>
                                    <th> {{ __('label.status') }} </th>
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
            var table = $('#table-transaction').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('transaction.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name',
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            let url_edit = "{{ route('transaction.edit', ':id') }}";
                            let url_destroy = "{{ route('transaction.destroy', ':id') }}";

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
                if ($.fn.DataTable.isDataTable('#table-transaction')) {
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
