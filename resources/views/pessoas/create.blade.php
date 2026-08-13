<x-layouts.app :title="'Nova pessoa'">

    <div class="max-w-2xl space-y-6">

        <h1 class="text-xl font-semibold text-ink">Nova pessoa</h1>

        <form method="POST" action="{{ route('pessoas.store') }}" class="space-y-6">
            @csrf

            <div class="bg-surface border border-border rounded-xl p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-ink-muted mb-2">Nome</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('name') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                    @error('name')
                        <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="person_type" class="block text-sm font-medium text-ink-muted mb-2">Tipo de pessoa</label>
                        <select id="person_type" name="person_type" required
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink focus:ring-primary/20 @error('person_type') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                            <option value="">Selecione</option>
                            <option value="PF" @selected(old('person_type') === 'PF')>Pessoa física</option>
                            <option value="PJ" @selected(old('person_type') === 'PJ')>Pessoa jurídica</option>
                        </select>
                        @error('person_type')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="document" class="block text-sm font-medium text-ink-muted mb-2">Documento (CPF/CNPJ)</label>
                        <input id="document" type="text" name="document" value="{{ old('document') }}" required
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('document') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('document')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-ink-muted mb-2">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('email') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-ink-muted mb-2">Telefone</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('phone') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('phone')
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
                        <input id="zip" type="text" name="zip" value="{{ old('zip') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('zip') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('zip')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="street" class="block text-sm font-medium text-ink-muted mb-2">Rua</label>
                        <input id="street" type="text" name="street" value="{{ old('street') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('street') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('street')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="number" class="block text-sm font-medium text-ink-muted mb-2">Número</label>
                        <input id="number" type="text" name="number" value="{{ old('number') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('number') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('number')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="district" class="block text-sm font-medium text-ink-muted mb-2">Bairro</label>
                        <input id="district" type="text" name="district" value="{{ old('district') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('district') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('district')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="complement" class="block text-sm font-medium text-ink-muted mb-2">Complemento</label>
                        <input id="complement" type="text" name="complement" value="{{ old('complement') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('complement') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('complement')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-ink-muted mb-2">Cidade</label>
                        <input id="city" type="text" name="city" value="{{ old('city') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('city') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('city')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-ink-muted mb-2">Estado</label>
                        <input id="state" type="text" name="state" value="{{ old('state') }}"
                            class="py-2.5 px-3.5 block w-full bg-canvas border rounded-xl sm:text-sm text-ink placeholder:text-ink-muted focus:ring-primary/20 @error('state') border-danger focus:border-danger @else border-border focus:border-primary @enderror">
                        @error('state')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit"
                class="py-2.5 px-5 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover focus:outline-hidden focus:ring-2 focus:ring-primary/40">
                Cadastrar pessoa
            </button>
        </form>

    </div>

</x-layouts.app>
