<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController
{
    public function index(Request $request): View
    {
        $filters = $request->only(['name', 'email', 'phone']);

        $customers = Customer::query()
            ->when(filled($filters['name'] ?? null), fn ($query) => $query->where('str_name', 'like', '%'.$filters['name'].'%'))
            ->when(filled($filters['email'] ?? null), fn ($query) => $query->where('str_email', 'like', '%'.$filters['email'].'%'))
            ->when(filled($filters['phone'] ?? null), function ($query) use ($filters) {
                $digits = preg_replace('/\D/', '', $filters['phone']);

                $query->where('str_phone', 'like', "%{$digits}%");
            })
            ->orderBy('str_name')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('customers.create', ['customer' => new Customer]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('profile_picture');
        $data['str_profile_picture_path'] = $request->file('profile_picture')
            ->store('customers', 'public');

        Customer::create($data);

        return redirect()
            ->route('customers.index')
            ->with('status', 'Cliente cadastrado com sucesso.');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->safe()->except('profile_picture');

        if ($request->hasFile('profile_picture')) {
            $customer->deleteProfilePicture();
            $data['str_profile_picture_path'] = $request->file('profile_picture')
                ->store('customers', 'public');
        }

        $customer->update($data);

        return redirect()
            ->route('customers.index')
            ->with('status', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->deleteProfilePicture();
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('status', 'Cliente excluído com sucesso.');
    }
}
