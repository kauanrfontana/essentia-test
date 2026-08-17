<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

it('exposes a public url for the stored profile picture', function () {
    Storage::fake('public');

    $customer = new Customer(['str_profile_picture_path' => 'customers/photo.jpg']);

    expect($customer->profilePictureUrl())->toContain('customers/photo.jpg');
});

it('returns null as picture url when the customer has no picture', function () {
    $customer = new Customer(['str_profile_picture_path' => null]);

    expect($customer->profilePictureUrl())->toBeNull();
});

it('deletes the stored picture file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('customers/photo.jpg', 'fake-content');

    $customer = new Customer(['str_profile_picture_path' => 'customers/photo.jpg']);
    $customer->deleteProfilePicture();

    Storage::disk('public')->assertMissing('customers/photo.jpg');
});

it('does nothing when deleting a picture from a customer without one', function () {
    Storage::fake('public');

    $customer = new Customer(['str_profile_picture_path' => null]);

    expect(fn () => $customer->deleteProfilePicture())->not->toThrow(Exception::class);
});

it('stores the phone as digits only regardless of the mask characters received', function () {
    $customer = new Customer(['str_phone' => '(48) 99999-0000']);

    expect($customer->getAttributes()['str_phone'])->toBe('48999990000');
});

it('formats an 11-digit phone as a mobile number', function () {
    $customer = new Customer(['str_phone' => '48999990000']);

    expect($customer->str_phone)->toBe('(48) 99999-0000');
});

it('formats a 10-digit phone as a landline number', function () {
    $customer = new Customer(['str_phone' => '4899990000']);

    expect($customer->str_phone)->toBe('(48) 9999-0000');
});

it('only allows the expected attributes to be mass assigned', function () {
    $customer = new Customer([
        'str_name' => 'Maria Silva',
        'str_email' => 'maria@example.com',
        'str_phone' => '(48) 99999-0000',
        'str_profile_picture_path' => 'customers/maria.jpg',
        'id' => 999,
    ]);

    expect($customer->str_name)->toBe('Maria Silva')
        ->and($customer->str_email)->toBe('maria@example.com')
        ->and($customer->str_phone)->toBe('(48) 99999-0000')
        ->and($customer->str_profile_picture_path)->toBe('customers/maria.jpg')
        ->and($customer->id)->toBeNull();
});
