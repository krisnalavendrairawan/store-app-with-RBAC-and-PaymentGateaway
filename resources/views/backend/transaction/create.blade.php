@extends('layouts.backend.index')

@section('title', $title)

@section('content')
    <div class="mb-3">
        <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
            <i class="mdi mdi-arrow-left"></i> Back to Transactions
        </a>
    </div>

    @if (isset($latestTransaction))
        <div id="statusAlert" class="alert alert-info" style="display: none;">
            Latest Transaction Status: {{ $latestTransaction->status }}
        </div>
    @endif

    <!-- Navbar for categories -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#categoryNavbar"
                aria-controls="categoryNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="categoryNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-category="all">All Products</a>
                    </li>
                    @foreach ($categories as $category)
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-category="{{ $category->id }}">{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>
                <form class="d-flex" id="searchForm">
                    <input class="form-control me-2" type="search" placeholder="Search products" aria-label="Search"
                        id="searchInput">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Product listing -->
    <div id="productList" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($products as $p)
            <div class="col product-item" data-category="{{ $p->category_id }}">
                <div class="card h-100">
                    <div class="card-img-top-wrapper" style="height: 200px; overflow: hidden;">
                        <img src="{{ asset('storage/' . $p->image) }}" class="card-img-top" alt="{{ $p->name }}"
                            style="object-fit: cover; height: 100%; width: 100%;">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between">
                            <p class="small"><a href="#!" class="text-muted">{{ $p->category->name }}</a></p>
                            <p class="small text-danger"><s>$1099</s></p>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">{{ $p->name }}</h5>
                            <h5 class="text-dark mb-0">{{ $p->price }}</h5>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <p class="text-muted mb-0">Available: <span class="fw-bold">{{ $p->quantity }}</span></p>
                        </div>

                        <div class="mt-auto">
                            <button type="button" class="btn btn-primary btn-sm w-100 mb-2 buy-button"
                                data-bs-toggle="modal" data-bs-target="#buyModal" data-product-id="{{ $p->id }}"
                                data-product-price="{{ $p->price }}">
                                Buy Now
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 add-to-cart-button"
                                data-product-id="{{ $p->id }}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- navigation button --}}
    <div id="loadMoreContainer" class="text-center mt-3" style="display: none;">
        <button id="loadMoreBtn" class="btn btn-primary">Load More</button>
    </div>

    <!-- Buy Modal -->
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
    <!-- Include Midtrans Snap library -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        function checkTransactionStatus(orderId) {
            $.ajax({
                url: '/check-transaction-status/' + orderId,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success',
                            text: 'Your payment has been processed successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // Check again after a few seconds
                        setTimeout(function() {
                            checkTransactionStatus(orderId);
                        }, 5000);
                    }
                },
                error: function() {
                    console.error('Error checking transaction status');
                }
            });
        }

        // When modal is shown
        $('#buyModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var productId = button.data('product-id');
            var productPrice = button.data('product-price');

            console.log('Selected product ID:', productId); // Console log the product ID

            var modal = $(this);
            modal.find('#product_id').val(productId);
            modal.find('#totalPrice').text('Rp.' + productPrice);

            $('#amount').on('input', function() {
                var amount = $(this).val();
                var totalPrice = amount * productPrice;
                modal.find('#totalPrice').text('Rp.' + totalPrice);
            });
        });

        $('#submitTransaction').on('click', function() {
            var form = $('#transactionForm');
            var amount = form.find('#amount').val();
            var productId = form.find('#product_id').val();

            $.ajax({
                url: "{{ route('transaction.store') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    amount: amount
                },
                success: function(response) {
                    if (response.token) {
                        snap.pay(response.token, {
                            onSuccess: function(result) {
                                console.log('Payment successful:', result);
                                // Update transaction status
                                $.ajax({
                                    url: "{{ route('transaction.updateStatus') }}",
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        order_id: result.order_id
                                    },
                                    success: function(updateResponse) {
                                        if (updateResponse.success) {
                                            Swal.fire({
                                                title: 'Success',
                                                text: 'Your payment has been processed successfully.',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            }).then((result) => {
                                                if (result
                                                    .isConfirmed) {
                                                    window.location
                                                        .href =
                                                        "{{ route('transaction.create') }}";
                                                }
                                            });
                                        } else {
                                            console.error(
                                                'Error updating transaction status:',
                                                updateResponse);
                                            Swal.fire({
                                                title: 'Warning',
                                                text: 'Payment successful, but there was an issue updating the transaction status.',
                                                icon: 'warning',
                                                confirmButtonText: 'OK'
                                            }).then((result) => {
                                                if (result
                                                    .isConfirmed) {
                                                    window.location
                                                        .href =
                                                        "{{ route('transaction.create') }}";
                                                }
                                            });
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(
                                            'Error updating transaction status:',
                                            error);
                                        Swal.fire({
                                            title: 'Warning',
                                            text: 'Payment successful, but there was an issue updating the transaction status.',
                                            icon: 'warning',
                                            confirmButtonText: 'OK'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href =
                                                    "{{ route('transaction.create') }}";
                                            }
                                        });
                                    }
                                });
                            },
                            onPending: function(result) {
                                console.log('Payment pending:', result);
                                Swal.fire({
                                    title: 'Pending',
                                    text: 'Your payment is pending. Please check your email for further instructions.',
                                    icon: 'info',
                                    confirmButtonText: 'OK'
                                });
                            },
                            onError: function(result) {
                                console.error('Payment failed:', result);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'An error occurred while processing your payment. Please try again later.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            },
                            onClose: function() {
                                console.log(
                                    'Customer closed the popup without finishing the payment'
                                );
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

        setTimeout(function() {
            $('#statusAlert').fadeIn('slow');

            setTimeout(function() {
                $('#statusAlert').fadeOut('slow');
            }, 2000); // Hide after 2 seconds
        }, 1000);

        $(document).ready(function() {
            // Category filter
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                $('.nav-link').removeClass('active');
                $(this).addClass('active');

                var category = $(this).data('category');
                if (category === 'all') {
                    $('.product-item').show();
                } else {
                    $('.product-item').hide();
                    $('.product-item[data-category="' + category + '"]').show();
                }
            });

            // Search functionality
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var searchTerm = $('#searchInput').val().toLowerCase();

                $('.product-item').each(function() {
                    var productName = $(this).find('h5').text().toLowerCase();
                    var productDescription = $(this).find('p').text().toLowerCase();

                    if (productName.includes(searchTerm) || productDescription.includes(
                            searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            var productItems = $('.product-item');
            var productsPerPage = 9;
            var currentlyShown = productsPerPage;

            productItems.hide().slice(0, productsPerPage).show();

            if (productItems.length > productsPerPage) {
                $('#loadMoreContainer').show();
            }

            $('#loadMoreBtn').on('click', function() {
                productItems.slice(currentlyShown, currentlyShown + productsPerPage).show();
                currentlyShown += productsPerPage;

                if (currentlyShown >= productItems.length) {
                    $('#loadMoreContainer').hide();
                }
            });
        });
    </script>
@endpush
