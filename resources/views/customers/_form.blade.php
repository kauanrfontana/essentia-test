@csrf

@if ($errors->any())
    <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-5">
    <div>
        <label for="str_name" class="mb-1 block text-sm font-medium">Nome</label>
        <input type="text" id="str_name" name="str_name" required maxlength="255"
               value="{{ old('str_name', $customer->str_name) }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none">
    </div>

    <div>
        <label for="str_email" class="mb-1 block text-sm font-medium">E-mail</label>
        <input type="email" id="str_email" name="str_email" required maxlength="255"
               value="{{ old('str_email', $customer->str_email) }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none">
    </div>

    <div>
        <label for="str_phone" class="mb-1 block text-sm font-medium">Telefone</label>
        <input type="text" id="str_phone" name="str_phone" required maxlength="20"
               placeholder="(48) 99999-9999" data-mask="phone" inputmode="numeric"
               value="{{ old('str_phone', $customer->str_phone) }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none">
    </div>

    <div>
        <label for="profile_picture" class="mb-1 block text-sm font-medium">
            Foto
            @if ($customer->exists)
                <span class="font-normal text-gray-500">(deixe em branco para manter a atual)</span>
            @endif
        </label>

        @if ($customer->profilePictureUrl())
            <img src="{{ $customer->profilePictureUrl() }}" alt="Foto atual de {{ $customer->str_name }}"
                 class="mb-2 size-20 rounded-full object-cover">
        @endif

        <input type="file" id="profile_picture" name="profile_picture"
               accept="image/jpeg,image/png,image/webp"
               @unless ($customer->exists) required @endunless
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm file:mr-3 file:rounded file:border-0 file:bg-gray-900 file:px-3 file:py-1 file:text-white">
        <p class="mt-1 text-xs text-gray-500">JPEG, PNG ou WebP, até 2 MB.</p>
    </div>
</div>

<div class="mt-8 flex items-center gap-3">
    <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
        Salvar
    </button>
    <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
</div>
