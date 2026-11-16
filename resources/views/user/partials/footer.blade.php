{{-- Rodapé compartilhado com navegação, pagamentos, contato e redes sociais. --}}
<footer class="site-footer">
    <div class="container-app footer-grid">
        <section>
            <h2>Navegação</h2>
            <ul class="footer-links">
                <li><a href="{{ route('sobre') }}">Sobre Nós</a></li>
                <li><a href="{{ route('favoritos.index') }}">Favoritos</a></li>
                @auth
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="footer-logout">
                            @csrf
                            <button type="submit">Sair da conta</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Minha Conta</a></li>
                @endauth
            </ul>
        </section>

        <section>
            <h2>Meios de Pagamento</h2>
            <div class="payment-grid">
                @foreach (config('mock.payments', []) as $pagamento)
                    <span class="payment-chip">{{ $pagamento }}</span>
                @endforeach
            </div>
        </section>

        <section class="footer-contact">
            <h2>Contato</h2>
            <p>Atendimento por e-mail ou WhatsApp:</p>
            <p>{{ config('mock.store.whatsapp') }}</p>
            <p>{{ config('mock.store.email') }}</p>
        </section>

        <section>
            <h2>Siga-nos</h2>
            <div class="social-links">
                <a href="#" class="social-link" data-coming-soon="Facebook" aria-label="Facebook">
                    <x-lucide-facebook aria-hidden="true" />
                </a>
                <a href="#" class="social-link" data-coming-soon="Instagram" aria-label="Instagram">
                    <x-lucide-instagram aria-hidden="true" />
                </a>
                <a href="#" class="social-link" data-coming-soon="TikTok" aria-label="TikTok">
                    <x-lucide-music-2 aria-hidden="true" />
                </a>
            </div>
        </section>
    </div>

    <div class="container-app footer-bottom">
        &copy; {{ date('Y') }} {{ config('mock.store.name') }}. Todos os direitos reservados.
    </div>
</footer>
