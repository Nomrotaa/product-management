@extends('layouts.app')

@section('content')
    <style>
        .error {
            color: red;
        }
    </style>

    <div class="container">
        <h1>Products</h1>

        <!-- Button to Open the Modal for Creating Product -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createProductModal">
            Add Product
        </button>

        <!-- Product Table -->
        <table class="table table-bordered" id="products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Product data will be populated here by DataTable -->
            </tbody>
        </table>

        <!-- Include the Create and Edit Modals -->
        @include('products.create')
        @include('products.edit')

        <!-- Show Product Modal -->
        <div class="modal fade" id="showProductModal" tabindex="-1" aria-labelledby="showProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showProductModalLabel">Product Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Title:</strong> <span id="product-title"></span></p>
                        <p><strong>Description:</strong> <span id="product-description"></span></p>
                        <p><strong>Price:</strong> $<span id="product-price"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <!-- Jquery Validate js -->
    <script src="{{ asset('assets/jqueryValidate/jquery.validate.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            // Initialize DataTable
            $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('products.index') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <button class="btn btn-primary edit-btn" data-id="${row.id}">Edit</button>
                            <button class="btn btn-danger delete-btn" data-id="${row.id}">Delete</button>
                            <button class="btn btn-info view-btn" data-id="${row.id}">View</button> <!-- Add View Button -->
                        `;
                        }
                    },
                ]
            });

            // Select the form element with the id attribute "submitForm"
            let selectedForm = $("#createProductForm");

            // Get the current URL of the window
            const BASE_URL = window.location.href;

            let validate = selectedForm.validate({
                rules: {
                    name: "required",
                },
                onsubmit: true,
            });

            // Create Product
            $(document).on('submit', '#createProductForm', function(e) {
                e.preventDefault();

                if (!validate.valid()) return;

                $(".error").remove();

                var formData = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        alert('Product created successfully!');
                        $('#createProductModal').modal('hide');
                        $('#products-table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        $('.error-text').text(''); // Clear previous errors
                        $.each(errors, function(field_name, error) {
                            $(document).find(`[name=${field_name}]`).after(
                                `<span class="text-danger error">${error}</span>`);
                        });
                    }
                });
            });

            // Edit Button Click Handler
            $(document).on('click', '.edit-btn', function() {
                var productId = $(this).data('id');
                $.ajax({
                    url: `/products/${productId}/edit`,
                    type: 'GET',
                    success: function(response) {
                        $('#editProductModal #product-id').val(response.id);
                        $('#editProductModal #title').val(response.title);
                        $('#editProductModal #description').val(response.description);
                        $('#editProductModal #price').val(response.price);
                        $('#editProductModal').modal('show');
                    },
                    error: function(xhr) {
                        alert('Failed to load product data.');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Update Product
            $(document).on('submit', '#editProductForm', function(e) {
                e.preventDefault();
                var productId = $('#editProductModal #product-id').val();
                var formData = $(this).serialize();
                $.ajax({
                    url: `/products/${productId}`,
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        alert('Product updated successfully!');
                        $('#editProductModal').modal('hide');
                        $('#products-table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        $('.error-text').text(''); // Clear previous errors
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $('.' + key + '-error').text(value[
                                0]); // Display validation errors
                        });
                    }
                });
            });

            // Delete Product
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure to delete this product?')) {
                    $.ajax({
                        url: '/products/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#products-table').DataTable().ajax.reload();
                            alert(response.success);
                        },
                        error: function(xhr) {
                            alert('Failed to delete product.');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });

            // View Product
            $(document).on('click', '.view-btn', function() {
                var productId = $(this).data('id');
                $.ajax({
                    url: `/products/${productId}`, // Adjust URL as needed
                    type: 'GET',
                    success: function(response) {
                        // Populate the modal with product details
                        $('#product-title').text(response.title);
                        $('#product-description').text(response.description);
                        $('#product-price').text(response.price.toFixed(
                            2)); // Format price to 2 decimal places
                        $('#showProductModal').modal('show'); // Show the modal
                    },
                    error: function(xhr) {
                        alert('Failed to load product data.');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
