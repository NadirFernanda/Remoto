{{--
    LGPD / GDPR Cookie Consent Banner
    - Stores decision in localStorage key "cookie_consent" ('accepted' | 'essential')
    - Visible until the user makes a choice
    - No server round-trip needed; purely client-side (Alpine.js)
--}}
<div
    x-data="{
        show: false,
        init() {
            const v = localStorage.getItem('cookie_consent');
            this.show = !v;
        },
        accept() {
            localStorage.setItem('cookie_consent', 'accepted');
            this.show = false;
        },
        essentialOnly() {
            localStorage.setItem('cookie_consent', 'essential');
            this.show = false;
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    style="display:none"
    class="fixed bottom-0 inset-x-0 z-50 p-4"
    role="dialog"
    aria-live="polite"
    aria-label="Aviso de cookies e privacidade"
>
    <div class="max-w-4xl mx-auto rounded-xl border border-white/10 bg-[#021018]/95 backdrop-blur-md shadow-2xl px-6 py-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <!-- Icon -->
        <div class="shrink-0 text-[#00baff] text-2xl" aria-hidden="true">🍪</div>

        <!-- Text -->
        <div class="flex-1 text-sm text-gray-300 leading-relaxed">
            <span class="font-semibold text-white">Privacidade &amp; Cookies</span>
            &nbsp;—&nbsp;
            Utilizamos cookies essenciais para o funcionamento da plataforma e, com o seu consentimento, cookies analíticos para melhorar a experiência.
            A sua privacidade é protegida nos termos da
            <strong class="text-white">Lei n.º&nbsp;22/11 (LGPD Angola)</strong>
            e do <strong class="text-white">RGPD</strong>.
            <a href="{{ route('legal.privacy') }}" class="text-[#00baff] hover:underline ml-1" target="_blank" rel="noopener">
                Política de Privacidade
            </a>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 shrink-0">
            <button
                @click="essentialOnly()"
                class="px-4 py-2 rounded-lg border border-white/20 text-sm text-gray-300 hover:bg-white/10 transition"
            >
                Apenas essenciais
            </button>
            <button
                @click="accept()"
                class="px-4 py-2 rounded-lg bg-[#00baff] hover:bg-[#00a8e6] text-[#021018] font-semibold text-sm transition"
            >
                Aceitar todos
            </button>
        </div>
    </div>
</div>
