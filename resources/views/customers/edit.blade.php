@extends('layouts.app')

@section('title', 'Editar cliente')

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-6">
        <h2 class="mb-6 text-lg font-semibold">Editar cliente</h2>

        <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data">
            @method('PUT')
            @include('customers._form', ['customer' => $customer])
        </form>
    </div>
@endsection
