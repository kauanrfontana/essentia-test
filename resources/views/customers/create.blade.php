@extends('layouts.app')

@section('title', 'Novo cliente')

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-6">
        <h2 class="mb-6 text-lg font-semibold">Novo cliente</h2>

        <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
            @include('customers._form', ['customer' => $customer])
        </form>
    </div>
@endsection
