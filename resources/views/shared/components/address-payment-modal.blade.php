{{-- Coleta entrega e pagamento e reabre quando a validação falha. --}}
@props([
    'pagamentos',
])

<dialog
    id="checkout-modal"
    class="checkout-modal"
    data-checkout-modal
    data-checkout-reopen="{{ $errors->any() || session('checkout_pos_login') ? 'true' : 'false' }}"
    aria-labelledby="checkout-modal-title"
>
    <div class="checkout-modal__panel">
        <header class="checkout-modal__header">
            <div>
                <p class="checkout-eyebrow">Último passo</p>
                <h2 id="checkout-modal-title">Endereço e pagamento</h2>
            </div>

            <button type="button" class="admin-modal-close" data-checkout-close aria-label="Fechar endereço e pagamento">
                <x-lucide-x aria-hidden="true" />
            </button>
        </header>

        <div class="checkout-modal__body">
            <fieldset class="checkout-modal__section">
                <legend>Endereço de entrega</legend>

                <div class="checkout-address-grid">
                    <div class="checkout-field checkout-field--cep">
                        <label for="cep">CEP <span class="required-mark" aria-hidden="true">*</span></label>
                        <input
                            id="cep"
                            name="cep"
                            class="campo"
                            type="text"
                            inputmode="numeric"
                            maxlength="9"
                            value="{{ old('cep') }}"
                            placeholder="00000-000"
                            autocomplete="postal-code"
                            required
                        >
                        @error('cep')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkout-field checkout-field--street">
                        <label for="endereco">Endereço <span class="required-mark" aria-hidden="true">*</span></label>
                        <input
                            id="endereco"
                            name="endereco"
                            class="campo"
                            type="text"
                            maxlength="255"
                            value="{{ old('endereco') }}"
                            placeholder="Rua, avenida ou travessa"
                            autocomplete="address-line1"
                            required
                        >
                        @error('endereco')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkout-field">
                        <label for="numero">Número <span class="required-mark" aria-hidden="true">*</span></label>
                        <input
                            id="numero"
                            name="numero"
                            class="campo"
                            type="text"
                            maxlength="20"
                            value="{{ old('numero') }}"
                            placeholder="123"
                            autocomplete="address-line2"
                            required
                        >
                        @error('numero')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkout-field">
                        <label for="complemento">Complemento</label>
                        <input
                            id="complemento"
                            name="complemento"
                            class="campo"
                            type="text"
                            maxlength="255"
                            value="{{ old('complemento') }}"
                            placeholder="Apto, bloco, casa..."
                            autocomplete="address-line3"
                        >
                        @error('complemento')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkout-field">
                        <label for="bairro">Bairro <span class="required-mark" aria-hidden="true">*</span></label>
                        <input
                            id="bairro"
                            name="bairro"
                            class="campo"
                            type="text"
                            maxlength="100"
                            value="{{ old('bairro') }}"
                            autocomplete="address-level3"
                            required
                        >
                        @error('bairro')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkout-field checkout-field--city">
                        <label for="cidade">Cidade <span class="required-mark" aria-hidden="true">*</span></label>
                        <input
                            id="cidade"
                            name="cidade"
                            class="campo"
                            type="text"
                            maxlength="100"
                            value="{{ old('cidade') }}"
                            autocomplete="address-level2"
                            required
                        >
                        @error('cidade')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkout-field checkout-field--state">
                        <label for="estado">UF <span class="required-mark" aria-hidden="true">*</span></label>
                        <input
                            id="estado"
                            name="estado"
                            class="campo"
                            type="text"
                            maxlength="2"
                            value="{{ old('estado') }}"
                            placeholder="SP"
                            autocomplete="address-level1"
                            required
                        >
                        @error('estado')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </fieldset>

            <fieldset class="checkout-modal__section" data-required-group>
                <legend>Forma de pagamento</legend>

                <div class="checkout-payment-options">
                    @foreach ($pagamentos as $pagamento)
                        <label class="checkout-payment-option">
                            <input
                                type="radio"
                                name="forma_pagamento"
                                value="{{ $pagamento }}"
                                class="sr-only checkout-payment-option__input"
                                @checked(old('forma_pagamento') === $pagamento)
                            >
                            <span class="checkout-payment-option__card">
                                <x-lucide-credit-card aria-hidden="true" />
                                <span>{{ $pagamento }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <span class="field-error" data-group-error hidden>Selecione a forma de pagamento.</span>

                @error('forma_pagamento')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <p class="checkout-payment-note">
                    Nenhum pagamento será realizado agora. O site está em testes e em construção;
                    nossa equipe combinará os detalhes com você.
                </p>
            </fieldset>
        </div>

        <footer class="checkout-modal__footer">
            <button type="button" class="btn btn-outline" data-checkout-close>Voltar</button>
            <button type="submit" class="btn btn-primary">Confirmar pedido</button>
        </footer>
    </div>
</dialog>
