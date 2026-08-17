<?php

use App\Models\Customer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

function customerPayload(array $overrides = []): array
{
    return array_merge([
        'str_name' => 'Maria Silva',
        'str_email' => 'maria@example.com',
        'str_phone' => '(48) 99999-0000',
        'profile_picture' => UploadedFile::fake()->image('maria.jpg'),
    ], $overrides);
}

describe('index', function () {
    it('lists the customers', function () {
        $customer = Customer::factory()->create(['str_name' => 'Maria Silva']);

        $this->get(route('customers.index'))
            ->assertOk()
            ->assertViewIs('customers.index')
            ->assertSee('Maria Silva')
            ->assertSee($customer->str_email);
    });

    it('shows the picture thumbnail next to the name', function () {
        Customer::factory()->create([
            'str_name' => 'Maria Silva',
            'str_profile_picture_path' => 'customers/maria.jpg',
        ]);

        $this->get(route('customers.index'))
            ->assertOk()
            ->assertSee('customers/maria.jpg');
    });

    it('shows an empty state when there is no customer', function () {
        $this->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Nenhum cliente cadastrado ainda.');
    });

    it('filters by name', function () {
        Customer::factory()->create(['str_name' => 'Maria Silva']);
        Customer::factory()->create(['str_name' => 'João Souza']);

        $this->get(route('customers.index', ['name' => 'Maria']))
            ->assertOk()
            ->assertSee('Maria Silva')
            ->assertDontSee('João Souza');
    });

    it('filters by email', function () {
        Customer::factory()->create(['str_name' => 'Maria Silva', 'str_email' => 'maria@example.com']);
        Customer::factory()->create(['str_name' => 'João Souza', 'str_email' => 'joao@other.com']);

        $this->get(route('customers.index', ['email' => 'example.com']))
            ->assertOk()
            ->assertSee('Maria Silva')
            ->assertDontSee('João Souza');
    });

    it('filters by phone regardless of mask characters in the stored value or the search term', function () {
        Customer::factory()->create(['str_name' => 'Maria Silva', 'str_phone' => '(48) 99999-0000']);
        Customer::factory()->create(['str_name' => 'João Souza', 'str_phone' => '(11) 98888-1234']);

        $this->get(route('customers.index', ['phone' => '48999990000']))
            ->assertOk()
            ->assertSee('Maria Silva')
            ->assertDontSee('João Souza');

        $this->get(route('customers.index', ['phone' => '(48) 99999-0000']))
            ->assertOk()
            ->assertSee('Maria Silva')
            ->assertDontSee('João Souza');
    });

    it('combines multiple filters', function () {
        Customer::factory()->create(['str_name' => 'Maria Silva', 'str_email' => 'maria@example.com']);
        Customer::factory()->create(['str_name' => 'Maria Souza', 'str_email' => 'outra@other.com']);

        $this->get(route('customers.index', ['name' => 'Maria', 'email' => 'example.com']))
            ->assertOk()
            ->assertSee('Maria Silva')
            ->assertDontSee('Maria Souza');
    });

    it('shows a filtered empty state when no customer matches', function () {
        Customer::factory()->create(['str_name' => 'Maria Silva']);

        $this->get(route('customers.index', ['name' => 'Inexistente']))
            ->assertOk()
            ->assertSee('Nenhum cliente encontrado para os filtros aplicados.');
    });
});

describe('create', function () {
    it('renders the creation form', function () {
        $this->get(route('customers.create'))
            ->assertOk()
            ->assertViewIs('customers.create')
            ->assertSee('name="str_name"', false)
            ->assertSee('name="profile_picture"', false);
    });
});

describe('store', function () {
    it('stores a customer and uploads the picture', function () {
        $response = $this->post(route('customers.store'), customerPayload());

        $response->assertRedirect(route('customers.index'))
            ->assertSessionHas('status');

        $customer = Customer::sole();

        expect($customer->str_name)->toBe('Maria Silva')
            ->and($customer->str_email)->toBe('maria@example.com')
            ->and($customer->str_phone)->toBe('(48) 99999-0000')
            ->and($customer->str_profile_picture_path)->toStartWith('customers/');

        Storage::disk('public')->assertExists($customer->str_profile_picture_path);
    });

    it('requires name, email, phone and picture', function () {
        $this->post(route('customers.store'), [])
            ->assertSessionHasErrors(['str_name', 'str_email', 'str_phone', 'profile_picture']);

        expect(Customer::count())->toBe(0);
    });

    it('rejects an invalid email', function () {
        $this->post(route('customers.store'), customerPayload(['str_email' => 'nao-e-email']))
            ->assertSessionHasErrors('str_email');
    });

    it('rejects a duplicated email', function () {
        Customer::factory()->create(['str_email' => 'maria@example.com']);

        $this->post(route('customers.store'), customerPayload())
            ->assertSessionHasErrors('str_email');

        expect(Customer::count())->toBe(1);
    });

    it('rejects a phone longer than 20 characters', function () {
        $this->post(route('customers.store'), customerPayload(['str_phone' => str_repeat('9', 21)]))
            ->assertSessionHasErrors('str_phone');
    });

    it('rejects a phone with an invalid digit count', function () {
        $this->post(route('customers.store'), customerPayload(['str_phone' => '123']))
            ->assertSessionHasErrors('str_phone');

        expect(Customer::count())->toBe(0);
    });

    it('stores the phone as digits only', function () {
        $this->post(route('customers.store'), customerPayload(['str_phone' => '(48) 99999-0000']));

        expect(Customer::sole()->getRawOriginal('str_phone'))->toBe('48999990000');
    });

    it('rejects a file that is not an image', function () {
        $this->post(route('customers.store'), customerPayload([
            'profile_picture' => UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('profile_picture');
    });

    it('rejects an image larger than 2mb', function () {
        $this->post(route('customers.store'), customerPayload([
            'profile_picture' => UploadedFile::fake()->image('grande.jpg')->size(2049),
        ]))->assertSessionHasErrors('profile_picture');
    });
});

describe('edit', function () {
    it('renders the edit form filled with the customer data', function () {
        $customer = Customer::factory()->create();

        $this->get(route('customers.edit', $customer))
            ->assertOk()
            ->assertViewIs('customers.edit')
            ->assertSee($customer->str_name, false)
            ->assertSee($customer->str_email, false);
    });
});

describe('update', function () {
    it('updates the customer keeping the current picture when none is sent', function () {
        $customer = Customer::factory()->create(['str_profile_picture_path' => 'customers/original.jpg']);
        Storage::disk('public')->put('customers/original.jpg', 'conteudo');

        $this->put(route('customers.update', $customer), [
            'str_name' => 'Nome Atualizado',
            'str_email' => 'atualizado@example.com',
            'str_phone' => '(48) 98888-0000',
        ])->assertRedirect(route('customers.index'));

        $customer->refresh();

        expect($customer->str_name)->toBe('Nome Atualizado')
            ->and($customer->str_email)->toBe('atualizado@example.com')
            ->and($customer->str_profile_picture_path)->toBe('customers/original.jpg');

        Storage::disk('public')->assertExists('customers/original.jpg');
    });

    it('replaces the picture and removes the old file when a new one is sent', function () {
        $customer = Customer::factory()->create(['str_profile_picture_path' => 'customers/original.jpg']);
        Storage::disk('public')->put('customers/original.jpg', 'conteudo');

        $this->put(route('customers.update', $customer), [
            'str_name' => $customer->str_name,
            'str_email' => $customer->str_email,
            'str_phone' => $customer->str_phone,
            'profile_picture' => UploadedFile::fake()->image('nova.jpg'),
        ])->assertRedirect(route('customers.index'));

        $customer->refresh();

        expect($customer->str_profile_picture_path)->not->toBe('customers/original.jpg');

        Storage::disk('public')->assertMissing('customers/original.jpg');
        Storage::disk('public')->assertExists($customer->str_profile_picture_path);
    });

    it('allows keeping the customer own email', function () {
        $customer = Customer::factory()->create(['str_email' => 'maria@example.com']);

        $this->put(route('customers.update', $customer), [
            'str_name' => 'Maria Silva',
            'str_email' => 'maria@example.com',
            'str_phone' => '(48) 99999-0000',
        ])->assertSessionHasNoErrors();
    });

    it('rejects an email already used by another customer', function () {
        Customer::factory()->create(['str_email' => 'outra@example.com']);
        $customer = Customer::factory()->create(['str_email' => 'maria@example.com']);

        $this->put(route('customers.update', $customer), [
            'str_name' => 'Maria Silva',
            'str_email' => 'outra@example.com',
            'str_phone' => '(48) 99999-0000',
        ])->assertSessionHasErrors('str_email');
    });
});

describe('destroy', function () {
    it('deletes the customer and its picture file', function () {
        $customer = Customer::factory()->create(['str_profile_picture_path' => 'customers/maria.jpg']);
        Storage::disk('public')->put('customers/maria.jpg', 'conteudo');

        $this->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('status');

        expect(Customer::count())->toBe(0);
        Storage::disk('public')->assertMissing('customers/maria.jpg');
    });

    it('deletes a customer without a picture', function () {
        $customer = Customer::factory()->withoutPicture()->create();

        $this->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'));

        expect(Customer::count())->toBe(0);
    });
});
