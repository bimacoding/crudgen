@extends('layouts.template')
@section('content')
    {!! Crud::render() !!}
    @once
        @push('ext_scripts')
            {!! Crud::renderjs() !!}
        @endpush
    @endonce
@endsection
