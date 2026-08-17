@extends('layouts.app')

@section('title', 'Clientes')

@section('header-action')
    <a href="{{ route('customers.create') }}"
       class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
        Novo cliente
    </a>
@endsection

@section('content')
    <form method="GET" action="{{ route('customers.index') }}"
          class="mb-6 grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-white p-4 sm:grid-cols-4">
        <div>
            <label for="filter_name" class="mb-1 block text-sm font-medium">Nome</label>
            <input type="text" id="filter_name" name="name" value="{{ $filters['name'] ?? '' }}"
                   placeholder="Buscar por nome"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none">
        </div>

        <div>
            <label for="filter_email" class="mb-1 block text-sm font-medium">E-mail</label>
            <input type="text" id="filter_email" name="email" value="{{ $filters['email'] ?? '' }}"
                   placeholder="Buscar por e-mail"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none">
        </div>

        <div>
            <label for="filter_phone" class="mb-1 block text-sm font-medium">Telefone</label>
            <input type="text" id="filter_phone" name="phone" value="{{ $filters['phone'] ?? '' }}"
                   placeholder="Buscar por telefone"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none">
        </div>

        <div class="flex items-end gap-3">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                Filtrar
            </button>
            @if (array_filter($filters))
                <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:underline">Limpar</a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3">Telefone</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($customer->profilePictureUrl())
                                    <img src="{{ $customer->profilePictureUrl() }}"
                                         alt="Foto de {{ $customer->str_name }}"
                                         class="size-10 shrink-0 rounded-full object-cover">
                                @else
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500">
                                        {{ mb_strtoupper(mb_substr($customer->str_name, 0, 1)) }}
                                    </span>
                                @endif
                                <span class="font-medium">{{ $customer->str_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $customer->str_email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $customer->str_phone }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('customers.edit', $customer) }}"
                                   class="font-medium text-blue-600 hover:underline">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                      onsubmit="return confirm('Excluir o cliente {{ $customer->str_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">
                            @if (array_filter($filters))
                                Nenhum cliente encontrado para os filtros aplicados.
                            @else
                                Nenhum cliente cadastrado ainda.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $customers->links() }}
    </div>
@endsection
