{{-- Mostra erros de validação fora das telas de acesso e cadastro. --}}
@if ($errors->any() && ! request()->routeIs('login', 'register'))
    <div class="container-app flash-area" data-flash-area>
        <div class="flash-message flash-message--error" role="alert">
            {{ $errors->first() }}
        </div>
    </div>
@endif
