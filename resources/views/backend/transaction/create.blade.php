@extends('layouts.backend.index')

@section('title', $title)

@section('content')
    @foreach ($product as $p)
        <div class="row justify-content-center mb-3">
            <div class="col-md-12 col-xl-10">
                <div class="card shadow-0 border rounded-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-lg-3 col-xl-3 mb-4 mb-lg-0">
                                <div class="bg-image hover-zoom ripple rounded ripple-surface">
                                    <img src="{{ asset('storage/' . $p->image) }}" alt="product" class="w-100" />
                                    <a href="#!">
                                        <div class="hover-overlay">
                                            <div class="mask" style="background-color: rgba(253, 253, 253, 0.15);"></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-6">
                                <h5>{{ $p->name }}</h5>
                                <div class="d-flex flex-row">
                                    <span>Unit : {{ $p->quantity }}</span>
                                </div>
                                <div class="mb-2 text-muted small">
                                    <span class="text-primary"> • </span>
                                    <span>{{ $p->category->name }}</span>
                                </div>
                                <p class="text-truncate mb-4 mb-md-0">
                                    {{ $p->description }}
                                </p>
                            </div>
                            <div class="col-md-6 col-lg-3 col-xl-3 border-sm-start-none border-start">
                                <div class="d-flex flex-row align-items-center mb-1">
                                    <h4 class="mb-1 me-1">Rp.{{ $p->price }}</h4>
                                </div>
                                <h6 class="text-success">Free shipping</h6>
                                <div class="d-flex flex-column mt-4">
                                    <button type="button" class="btn btn-primary buy-button" data-bs-toggle="modal"
                                        data-bs-target="#buyModal" data-product-id="{{ $p->id }}"
                                        data-product-price="{{ $p->price }}">
                                        Buy
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm mt-2" type="button">
                                        Add to wishlist
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="buyModal" tabindex="-1" aria-labelledby="buyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="buyModalLabel">Purchase Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="transactionForm">
                        @csrf
                        <input type="hidden" name="product_id" id="product_id">
                        <x-input-text name="amount" id="amount" label="Amount" placeholder="Enter amount" type="number"
                            :old="old('amount')" value="1" min="1" />
                        <h5>Total: <span id="totalPrice"></span></h5>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitTransaction">Buy</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include Midtrans Snap library -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('SB-Mid-client-v3zTcDPp5IVngBYZ') }}"></script>

    <script>
        // When modal is shown
        $('#buyModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var productId = button.data('product-id');
            var productPrice = button.data('product-price');

            console.log('Selected product ID:', productId); // Console log the product ID

            var modal = $(this);
            modal.find('#product_id').val(productId);
            modal.find('#totalPrice').text('Rp.' + productPrice);

            // Calculate the total price when the amount is changed
            $('#amount').on('input', function() {
                var amount = $(this).val();
                var totalPrice = amount * productPrice;
                modal.find('#totalPrice').text('Rp.' + totalPrice);
            });
        });

        // Handle form submission
        $('#submitTransaction').on('click', function() {
            var form = $('#transactionForm');
            var amount = form.find('#amount').val();
            var productId = form.find('#product_id').val();

            // Call the backend to create a transaction
            $.ajax({
                url: "{{ route('transaction.store') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    amount: amount
                },
                success: function(response) {
                    if(response.token){
                        // Open Snap payment popup
                        snap.pay(response.token, {
                            // Callback function when the transaction is finished
                            onSuccess: function(result) {
                                console.log('Payment successful:', result);
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Your payment has been processed successfully.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                            },
                            // Callback function when the transaction is pending
                            onPending: function(result) {
                                console.log('Payment pending:', result);
                                Swal.fire({
                                    title: 'Pending',
                                    text: 'Your payment is pending. Please check your email for further instructions.',
                                    icon: 'info',
                                    confirmButtonText: 'OK'
                                });
                            },
                            // Callback function when the transaction is failed
                            onError: function(result) {
                                console.error('Payment failed:', result);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'An error occurred while processing your payment. Please try again later.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    } else {
                        console.error('Error:', response);
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while processing your payment. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while processing your payment. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endpush
