<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel 11 Ajax</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/2.1.5/css/dataTables.bootstrap5.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="#">Laravel 11 Ajax</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                    </li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Products</h4>
                    </div>
                    <div class="card-body overflow-auto">
                        <button class="btn btn-primary mb-3" onclick="showModal()">Create Product</button>
                        <table class="table table-bordered table-striped" id="tableProduct">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Slug Url</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    {{-- <th>Image</th> --}}
                                    <th>action</th>
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

    @include('products.modalTable')

    <script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.5/js/dataTables.bootstrap5.js"></script>

    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\ProductRequest', '#productForm') !!}
    <script>
        let saveMethod;
        $(document).ready(function() {

            productTable();

        });

        function productTable() {
            $('#tableProduct').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('products.serverSideTable') }}",
                    type: 'GET',
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'slug',
                        name: 'slug'
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
                        data: 'image',
                        name: 'image',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }


        function resetValidation() {
            $('.is-invalid').removeClass('is-invalid');
            $('.is-valid').removeClass('is-valid');
            $('.span-invalid-feedback').remove();
        }

        function showModal() {
            $('.modal-title').text('Create New Product');
            saveMethod = "create";
            resetValidation();
            $('#productForm')[0].reset();
            $('#productModal').modal('show');
            $('.btnSubmitModal').text('Create Product');
        }

        // Create or Update Product
        $('#productForm').on('submit',function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var url, method;
            url = 'products';
            method = 'POST';
            if (saveMethod == 'update') {
                url = "products/" + $('#id').val();
                formData.append('_method', 'PUT');
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: method,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#productModal').modal('hide');
                    $('#productForm').trigger('reset');
                    $('#tableProduct').DataTable().ajax.reload();
                    Swal.fire({
                        position: "center",
                        icon: response.icon,
                        title: response.title,
                        text: response.text,
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                    alert("Error" + jqXHR.responseText);

                }
            });
        });

        function deleteModal(e){
            const id = $(e).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "products/"+id,
                        type: 'DELETE',
                        success: function(response) {
                            $('#tableProduct').DataTable().ajax.reload();
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Success!",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function(error) {
                            console.log('Error:', error);
                            Swal.fire(
                                'Error!',
                                'Failed to delete product!',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function editModal(e){
            let id = e.getAttribute('data-id');
            saveMethod = 'update';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "products/"+id,
                type: 'GET',
                success: function(response) {
                    let result = response.data;
                    $('.modal-title').text('Update Product');
                    $('#productModal').modal('show');
                    $('.btnSubmitModal').text('Update Product');
                    $('#name').val(result.name);
                    $('#description').val(result.description);
                    $('#price').val(result.price);
                    // $('#image').val(result.image);
                    $('#id').val(result.uuid);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                    alert("Error" + jqXHR.responseText);
                }
            });
        }

    </script>


</body>

</html>
