@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container--md" style="padding-top:0.75rem;padding-bottom:4rem;">

        <div class="pub-hero" style="margin-bottom:2.5rem;">
            <div class="pub-hero-label">Pessoas</div>
            <h1 class="pub-hero-title">A nossa equipa</h1>
            <p class="pub-hero-sub">Conheça as pessoas apaixonadas que constroem e evoluem a 24 Horas Remoto todos os dias.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 text-center border border-[#e6f3fa] transition hover:shadow-2xl">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#00baff] to-[#0077cc] flex items-center justify-center text-white font-black text-3xl mx-auto mb-4">N</div>
                <div class="font-extrabold text-[#0f172a] text-lg">Nadir Fernanda</div>
                <div class="text-[#00baff] text-xs font-bold my-1 mb-3">CEO &amp; Co-fundadora</div>
                <p class="text-[#64748b] text-base leading-relaxed max-w-xs mx-auto mt-2">Visionária por trás da 24 Horas Remoto, com experiência em empreendedorismo digital e desenvolvimento de produto na África Subsaariana.</p>
            </div>
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 text-center border border-[#e6f3fa] transition hover:shadow-2xl">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#06b6d4] to-[#0077cc] flex items-center justify-center text-white font-black text-3xl mx-auto mb-4">P</div>
                <div class="font-extrabold text-[#0f172a] text-lg">Paulo Mendes</div>
                <div class="text-[#00baff] text-xs font-bold my-1 mb-3">CTO &amp; Co-fundador</div>
                <p class="text-[#64748b] text-base leading-relaxed max-w-xs mx-auto mt-2">Engenheiro de software com mais de 10 anos de experiência em plataformas de marketplace e sistemas de pagamento seguros.</p>
            </div>
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 text-center border border-[#e6f3fa] transition hover:shadow-2xl">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#10b981] to-[#0077cc] flex items-center justify-center text-white font-black text-3xl mx-auto mb-4">C</div>
                <div class="font-extrabold text-[#0f172a] text-lg">Catarina Lopes</div>
                <div class="text-[#00baff] text-xs font-bold my-1 mb-3">Directora de Produto</div>
                <p class="text-[#64748b] text-base leading-relaxed max-w-xs mx-auto mt-2">Especialista em UX e growth, responsável por transformar feedback de utilizadores em funcionalidades que fazem a diferença real.</p>
            </div>
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 text-center border border-[#e6f3fa] transition hover:shadow-2xl">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#f59e0b] to-[#0077cc] flex items-center justify-center text-white font-black text-3xl mx-auto mb-4">R</div>
                <div class="font-extrabold text-[#0f172a] text-lg">Roberto Silva</div>
                <div class="text-[#00baff] text-xs font-bold my-1 mb-3">Director de Operações</div>
                <p class="text-[#64748b] text-base leading-relaxed max-w-xs mx-auto mt-2">Garante que cada transação, debate e resolução de disputa ocorre com eficiência e justiça para ambas as partes.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 text-center border border-[#e6f3fa] transition hover:shadow-2xl">
            <h2 class="text-xl md:text-2xl font-extrabold text-[#0f172a] mb-2">Quer fazer parte da nossa equipa?</h2>
            <p class="text-[#64748b] mb-4 text-lg">Estamos sempre à procura de talentos apaixonados pela nossa missão.</p>
            <a href="{{ route('sobre.carreiras') }}" class="inline-block bg-[#00baff] hover:bg-[#009ad6] text-white font-extrabold px-8 py-3 rounded-xl text-lg transition">Ver vagas abertas</a>
        </div>

    </div>
</div>
@endsection
