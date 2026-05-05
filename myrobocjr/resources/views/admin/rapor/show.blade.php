@extends('layouts.app')

@section('header')
Detail Rapor
@endsection

@section('content')

@include('admin.rapor._form', [
    'rapor'       => $rapor,
    'materis'     => $materis,
    'kompetensis' => $kompetensis,
    'readonly'    => true
])

@endsection
