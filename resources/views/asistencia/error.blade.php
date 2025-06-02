@extends('layout_especialista/cuerpo')

@section('html')

<div class="container d-flex justify-content-center ">

    <div class="text-center col-md-12">
        <h1>@isset($error)
            {{$error}}
        @endisset</h1>
    </div>
</div>
@endsection
