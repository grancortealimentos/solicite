<div class="bg-surface border border-border rounded-xl p-6 space-y-4">
    <div class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input id="is_active" type="checkbox" name="is_active" value="1"
            @checked(old('is_active', $company->is_active ?? true))
            class="size-4 rounded border-border text-primary focus:ring-primary/20">
        <label for="is_active" class="text-sm font-medium text-ink-muted">Filial ativa</label>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="code" class="block text-sm font-medium text-ink-muted mb-2">Código</label>
            <input id="code" type="text" name="code" value="{{ old('code', $company->code) }}" required autofocus
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('code') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('code')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-ink-muted mb-2">Nome</label>
            <input id="name" type="text" name="name" value="{{ old('name', $company->name) }}" required
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('name') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('name')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="trade_name" class="block text-sm font-medium text-ink-muted mb-2">Nome fantasia</label>
        <input id="trade_name" type="text" name="trade_name" value="{{ old('trade_name', $company->trade_name) }}"
            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('trade_name') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
        @error('trade_name')
            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="cnpj" class="block text-sm font-medium text-ink-muted mb-2">CNPJ</label>
            <input id="cnpj" type="text" name="cnpj" value="{{ old('cnpj', $company->cnpj) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('cnpj') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('cnpj')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="ie" class="block text-sm font-medium text-ink-muted mb-2">Inscrição estadual</label>
            <input id="ie" type="text" name="ie" value="{{ old('ie', $company->ie) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('ie') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('ie')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="bg-surface border border-border rounded-xl p-6 space-y-4">
    <h2 class="text-sm font-semibold text-ink">Endereço</h2>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="zip" class="block text-sm font-medium text-ink-muted mb-2">CEP</label>
            <input id="zip" type="text" name="zip" value="{{ old('zip', $company->zip) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('zip') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('zip')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-span-2">
            <label for="street" class="block text-sm font-medium text-ink-muted mb-2">Rua</label>
            <input id="street" type="text" name="street" value="{{ old('street', $company->street) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('street') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('street')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="number" class="block text-sm font-medium text-ink-muted mb-2">Número</label>
            <input id="number" type="text" name="number" value="{{ old('number', $company->number) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('number') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('number')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="district" class="block text-sm font-medium text-ink-muted mb-2">Bairro</label>
            <input id="district" type="text" name="district" value="{{ old('district', $company->district) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('district') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('district')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="complement" class="block text-sm font-medium text-ink-muted mb-2">Complemento</label>
            <input id="complement" type="text" name="complement" value="{{ old('complement', $company->complement) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('complement') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('complement')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="city" class="block text-sm font-medium text-ink-muted mb-2">Cidade</label>
            <input id="city" type="text" name="city" value="{{ old('city', $company->city) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('city') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('city')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="state" class="block text-sm font-medium text-ink-muted mb-2">Estado</label>
            <input id="state" type="text" name="state" value="{{ old('state', $company->state) }}"
                class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('state') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
            @error('state')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
