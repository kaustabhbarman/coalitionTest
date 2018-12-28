<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Coalition Technologies</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                Coalition Technologies
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Enter Inventory Details</div>

                        <div class="card-body">
                            <form action="{{ url('/submit')}}" method="POST" id="myForm">
                                @csrf

                                <div class="form-group row">
                                    <label for="product" class="col-md-4 col-form-label text-md-right">{{ __('Product') }}</label>

                                    <div class="col-md-6">
                                        <input name="product" id="product" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required autofocus>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="quantity" class="col-md-4 col-form-label text-md-right">{{ __('Quantity') }}</label>

                                    <div class="col-md-6">
                                        <input id="quantity" type="text" class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}" name="quantity" value="{{ old('quantity') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="price" class="col-md-4 col-form-label text-md-right">Price</label>

                                    <div class="col-md-6">
                                        <input id="price" type="text" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button id="submit" type="button" class="btn btn-primary">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div><br>
            <table class="table table-striped" id="dataTable">
                <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Price per Item</th>
                    <th>DateTime Submitted</th>
                    <th>Total Value</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @if($inventory)
                    @foreach($inventory as $item)
                        <tr id="{{$item->datetime}}">
                            <td class="align-middle" id="{{$item->product}}">{{$item->product}}</td>
                            <td class="align-middle">{{$item->quantity}}</td>
                            <td class="align-middle">{{$item->price}}</td>
                            <td class="align-middle">{{$item->datetime}}</td>
                            <td class="align-middle">{{$item->totalvalue}}</td>
                            <td class="align-middle">
                                <button type="button" class="btn btn-default" id="edit" data-name="{{$item->product}}" data-quantity="{{$item->quantity}}" data-price="{{$item->price}}" >Edit</button>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <table class="table table-striped" id="totalTable">
                <tr>
                    <td class="align-middle" id=""><b>Total</b></td>
                    <td class="align-middle"></td>
                    <td class="align-middle"></td>
                    <td class="align-middle"></td>
                    <td id="total" class="align-middle">{{$total}}</td>
                    <td class="align-middle"></td>
                </tr>
            </table>
        </div>
    </main>
</div>
</body>

<script>
    $(document).on('click', '#submit', function () {
        var price = $('#price').val();
        var quantity = $('#quantity').val();
        if (!(/\D/.test(price)) && !(/\D/.test(quantity))) {
            $.ajax({
                type: 'post',
                url: '/submit',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'product': $('#product').val(),
                    'quantity': $('#quantity').val(),
                    'price': $('#price').val()
                },
                dataType: 'json',
                error: function(data){
                    window.alert(data.status + ': Please fill all the fields');
                },
                success: function (data) {
                    if (data.status == 401){
                        window.alert(data.message);
                    }else {
                        var tr = '<tr id=' + data.row.datetime + '><td class="align-middle">' + data.row.product + '</td>';
                        tr += '<td class="align-middle">' + data.row.quantity + '</td>';
                        tr += '<td class="align-middle">' + data.row.price + '</td>';
                        tr += '<td class="align-middle">' + data.row.datetime + '</td>';
                        tr += '<td class="align-middle">' + data.row.totalvalue + '</td>';
                        tr += '<td class="align-middle"><button type="button" class="btn btn-default" id="edit" data-name="' + data.row.product + '" data-quantity="' + data.row.quantity + '" data-price="' + data.row.price + '" >Edit</button></td>';
                        $('#dataTable').append(tr);
                        var mytable = document.getElementById("totalTable");
                        mytable.rows[0].cells[4].innerHTML = data.total;
                        document.getElementById("myForm").reset();
                        console.log(data);
                    }
                }
            });
        } else {
            window.alert("Please enter only numeric value in Quantity and Price field");
        }
    })
</script>
</html>
