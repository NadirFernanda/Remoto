<header x-data="{open:false, scrolled:false, progress:0}" x-init="
    scrolled = window.location.pathname !== '/';
    window.addEventListener('scroll', ()=>{
        scrolled = window.location.pathname !== '/' || window.scrollY > 30;
        var max = document.documentElement.scrollHeight - window.innerHeight;
        progress = max > 0 ? Math.round(window.scrollY / max * 100) : 0;
    })" :class="{'scrolled': scrolled}" class="site-header fixed top-0 left-0 z-50 w-full">
    <!-- Barra de progresso de scroll -->
    <div class="scroll-progress-bar" :style="'width:' + progress + '%'"></div>
    <div class="header-container px-4">

        <!-- Esquerda: Logo + Nav agrupados -->
        <div style="display:flex;align-items:center;gap:0;flex-shrink:0;">
            <a href="/" class="flex items-center" aria-label="24 Horas" style="margin-right:1.5rem;">
                <img src="{{ asset('img/logo.png') }}" alt="24 Horas" class="site-logo">
            </a>

            @guest
            <nav class="nav-desktop" style="align-items:center;gap:0.25rem;margin-left:0;">
                {{-- ============================================================ --}}
                {{-- DROPDOWN 1: CONTRATAR --}}
                {{-- Comportamento: hover no item esquerdo troca painel direito --}}
                {{-- ============================================================ --}}
                <div x-data="{open:false, tab:'habilidade'}" class="relative">
                    <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:0.35rem;white-space:nowrap;">
                        Contratar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute left-0 mt-2 rounded-2xl z-50"
                         style="width:800px;max-width:calc(100vw - 2rem);background:#141928;box-shadow:0 24px 64px rgba(0,0,0,.5);border:1px solid rgba(255,255,255,.08);">
                        <div style="display:flex;">
                            <!-- Coluna esquerda: tabs de navegação -->
                            <div style="width:280px;padding:1.5rem 1.125rem;border-right:1px solid rgba(255,255,255,.07);flex-shrink:0;">
                                <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Encontrar profissionais</p>
                                <!-- Tab: Por habilidade -->
                                <div @mouseenter="tab='habilidade'" @click="tab='habilidade'"
                                     :style="tab==='habilidade' ? 'background:rgba(0,153,214,.15);border-radius:.875rem;' : ''"
                                     style="display:flex;align-items:flex-start;gap:.875rem;padding:.7rem .875rem;border-radius:.875rem;cursor:pointer;transition:background .15s;margin-bottom:.25rem;">
                                    <span style="width:38px;height:38px;border-radius:9px;background:rgba(0,153,214,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="19" height="19" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.85rem;">Por habilidade</span>
                                        <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.15rem;line-height:1.45;">Procura um profissional com uma habilidade específica?</span>
                                    </span>
                                    <svg width="15" height="15" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                                <!-- Tab: Por localização -->
                                <div @mouseenter="tab='localizacao'" @click="tab='localizacao'"
                                     :style="tab==='localizacao' ? 'background:rgba(0,153,214,.15);border-radius:.875rem;' : ''"
                                     style="display:flex;align-items:flex-start;gap:.875rem;padding:.7rem .875rem;border-radius:.875rem;cursor:pointer;transition:background .15s;margin-bottom:.25rem;">
                                    <span style="width:38px;height:38px;border-radius:9px;background:rgba(0,153,214,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="19" height="19" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.85rem;">Por localização</span>
                                        <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.15rem;line-height:1.45;">Pesquise com base na localização e fuso horário.</span>
                                    </span>
                                    <svg width="15" height="15" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                                <!-- Tab: Por categoria -->
                                <div @mouseenter="tab='categoria'" @click="tab='categoria'"
                                     :style="tab==='categoria' ? 'background:rgba(0,153,214,.15);border-radius:.875rem;' : ''"
                                     style="display:flex;align-items:flex-start;gap:.875rem;padding:.7rem .875rem;border-radius:.875rem;cursor:pointer;transition:background .15s;">
                                    <span style="width:38px;height:38px;border-radius:9px;background:rgba(0,153,214,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="19" height="19" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.85rem;">Por categoria</span>
                                        <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.15rem;line-height:1.45;">Encontre profissionais para determinada categoria de projecto.</span>
                                    </span>
                                    <svg width="15" height="15" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                            </div>
                            <!-- Coluna direita: painéis de cards por tab -->
                            <div style="flex:1;padding:1.5rem 1.125rem;min-height:380px;display:flex;flex-direction:column;">
                                <!-- Painel: habilidade -->
                                <div x-show="tab==='habilidade'">
                                    <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Profissionais por habilidade</p>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;">
                                        <a href="{{ route('freelancers.search', ['skill' => 'design']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?w=240&h=90&fit=crop&auto=format" alt="Design" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Designers Gráficos</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['skill' => 'web']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1547658719-da2b51169166?w=240&h=90&fit=crop&auto=format" alt="Web" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Dev. de Websites</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['skill' => 'mobile']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=240&h=90&fit=crop&auto=format" alt="Mobile" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Apps Mobile</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['skill' => 'video']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=240&h=90&fit=crop&auto=format" alt="Video" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Edição de Vídeo</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['skill' => 'marketing']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=240&h=90&fit=crop&auto=format" alt="Marketing" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Marketing Digital</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['skill' => 'redacao']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?w=240&h=90&fit=crop&auto=format" alt="Redacao" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Redação & Conteúdo</div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Painel: localização -->
                                <div x-show="tab==='localizacao'" style="display:none;">
                                    <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Freelancers por localização</p>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;">
                                        <a href="{{ route('freelancers.search', ['location' => 'luanda']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=240&h=90&fit=crop&auto=format" alt="Luanda" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Luanda</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['location' => 'benguela']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1470770841072-f978cf4d019e?w=240&h=90&fit=crop&auto=format" alt="Benguela" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Benguela</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['location' => 'huambo']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=240&h=90&fit=crop&auto=format" alt="Huambo" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Huambo</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['location' => 'lobito']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1500835556837-99ac94a94552?w=240&h=90&fit=crop&auto=format" alt="Lobito" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Lobito</div>
                                        </a>
                                        <a href="{{ route('freelancers.search', ['location' => 'cabinda']) }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1508739773434-c26b3d09e071?w=240&h=90&fit=crop&auto=format" alt="Cabinda" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Cabinda</div>
                                        </a>
                                        <a href="{{ route('freelancers.search') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;background:#1e293b;display:flex;align-items:center;justify-content:center;"><svg width="28" height="28" fill="none" stroke="#0099d6" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#0099d6;">Ver todas →</div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Painel: categoria -->
                                <div x-show="tab==='categoria'" style="display:none;">
                                    <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Categorias de projectos</p>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;">
                                        <a href="{{ route('freelancers.index') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=240&h=90&fit=crop&auto=format" alt="Tech" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Tecnologia</div>
                                        </a>
                                        <a href="{{ route('freelancers.index') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1542744094-3a31f272c490?w=240&h=90&fit=crop&auto=format" alt="Criativo" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Criativo</div>
                                        </a>
                                        <a href="{{ route('freelancers.index') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=240&h=90&fit=crop&auto=format" alt="Negocios" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Negócios</div>
                                        </a>
                                        <a href="{{ route('freelancers.index') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=240&h=90&fit=crop&auto=format" alt="Educacao" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Educação</div>
                                        </a>
                                        <a href="{{ route('freelancers.index') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=240&h=90&fit=crop&auto=format" alt="Juridico" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Jurídico</div>
                                        </a>
                                        <a href="{{ route('freelancers.index') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.18)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=240&h=90&fit=crop&auto=format" alt="Engenharia" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e293b;padding:.45rem .7rem;font-size:.75rem;font-weight:700;color:#f1f5f9;">Engenharia</div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Footer Contratar -->
                                <div style="border-top:1px solid rgba(255,255,255,.07);padding-top:.75rem;margin-top:auto;display:flex;align-items:center;justify-content:space-between;">
                                    <span style="font-size:.75rem;font-weight:600;color:#94a3b8;">Explorar todos os freelancers</span>
                                    <a href="{{ route('freelancers.index') }}" style="font-size:.75rem;font-weight:700;color:#0099d6;text-decoration:none;display:flex;align-items:center;gap:.25rem;">Ver todos <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-data="{open:false, tab:'habilidade'}" class="relative">
                    <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:0.35rem;white-space:nowrap;">
                        Encontrar trabalho
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute left-0 mt-2 rounded-2xl z-50"
                         style="width:760px;max-width:calc(100vw - 2rem);background:#141928;box-shadow:0 24px 64px rgba(0,0,0,.5);border:1px solid rgba(255,255,255,.08);">
                        <div style="display:flex;">
                            <!-- Coluna esquerda: tabs -->
                            <div style="width:280px;padding:1.5rem 1.125rem;border-right:1px solid rgba(255,255,255,.07);flex-shrink:0;">
                                <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Encontrar trabalho</p>
                                <!-- Tab: Por habilidade -->
                                <div @mouseenter="tab='habilidade'" @click="tab='habilidade'"
                                     :style="tab==='habilidade' ? 'background:rgba(0,153,214,.15);border-radius:.875rem;' : ''"
                                     style="display:flex;align-items:flex-start;gap:.875rem;padding:.7rem .875rem;border-radius:.875rem;cursor:pointer;transition:background .15s;margin-bottom:.25rem;">
                                    <span style="width:38px;height:38px;border-radius:9px;background:rgba(0,153,214,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="19" height="19" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.85rem;">Por habilidade</span>
                                        <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.15rem;line-height:1.45;">Pesquise trabalhos que exigem uma habilidade específica.</span>
                                    </span>
                                    <svg width="15" height="15" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                                <!-- Tab: Por idioma -->
                                <div @mouseenter="tab='idioma'" @click="tab='idioma'"
                                     :style="tab==='idioma' ? 'background:rgba(0,153,214,.15);border-radius:.875rem;' : ''"
                                     style="display:flex;align-items:flex-start;gap:.875rem;padding:.7rem .875rem;border-radius:.875rem;cursor:pointer;transition:background .15s;margin-bottom:.25rem;">
                                    <span style="width:38px;height:38px;border-radius:9px;background:rgba(0,153,214,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="19" height="19" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.85rem;">Por idioma</span>
                                        <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.15rem;line-height:1.45;">Encontre projectos no seu idioma preferido.</span>
                                    </span>
                                    <svg width="15" height="15" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                                <!-- Tab: Trabalhos em destaque -->
                                <div @mouseenter="tab='destaque'" @click="tab='destaque'"
                                     :style="tab==='destaque' ? 'background:rgba(0,153,214,.15);border-radius:.875rem;' : ''"
                                     style="display:flex;align-items:flex-start;gap:.875rem;padding:.7rem .875rem;border-radius:.875rem;cursor:pointer;transition:background .15s;margin-bottom:.25rem;">
                                    <span style="width:38px;height:38px;border-radius:9px;background:rgba(0,153,214,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="19" height="19" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.85rem;">Trabalhos em destaque</span>
                                        <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.15rem;line-height:1.45;">Explore os melhores projectos disponíveis agora.</span>
                                    </span>
                                    <svg width="15" height="15" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                                <!-- Tab: Encontrar concursos -->
                                <div @mouseenter="tab='concursos'" @click="tab='concursos'"
                                     :style="tab==='concursos' ? 'background:rgba(0,153,214,.15);border-radius:.875rem;' : ''"
                                     style="display:flex;align-items:flex-start;gap:.875rem;padding:.7rem .875rem;border-radius:.875rem;cursor:pointer;transition:background .15s;">
                                    <span style="width:38px;height:38px;border-radius:9px;background:rgba(0,153,214,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="19" height="19" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </span>
                                    <span style="flex:1;">
                                        <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.85rem;">Encontrar concursos</span>
                                        <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.15rem;line-height:1.45;">Participe em concursos e ganhe projectos.</span>
                                    </span>
                                    <svg width="15" height="15" fill="none" stroke="#374151" stroke-width="2.5" viewBox="0 0 24 24" style="margin-top:3px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                            </div>
                            <!-- Coluna direita: painéis por tab -->
                            <div style="flex:1;padding:1.5rem 1.125rem;min-height:380px;display:flex;flex-direction:column;">
                                <!-- Painel: habilidade -->
                                <div x-show="tab==='habilidade'">
                                    <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Trabalhos por habilidade</p>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;margin-bottom:.875rem;">
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1547658719-da2b51169166?w=240&h=90&fit=crop&auto=format" alt="Web" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Websites</div>
                                        </a>
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?w=240&h=90&fit=crop&auto=format" alt="Design" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Design Gráfico</div>
                                        </a>
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=240&h=90&fit=crop&auto=format" alt="Marketing" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Marketing Digital</div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Painel: idioma -->
                                <div x-show="tab==='idioma'" style="display:none;">
                                    <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Trabalhos por idioma</p>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;margin-bottom:.875rem;">
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;background:#1e293b;display:flex;align-items:center;justify-content:center;font-size:2.2rem;">🇦🇴</div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Português</div>
                                        </a>
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;background:#1e293b;display:flex;align-items:center;justify-content:center;font-size:2.2rem;">🇬🇧</div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Inglês</div>
                                        </a>
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;background:#1e293b;display:flex;align-items:center;justify-content:center;font-size:2.2rem;">🇫🇷</div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Francês</div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Painel: destaque -->
                                <div x-show="tab==='destaque'" style="display:none;">
                                    <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Projectos em destaque</p>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;margin-bottom:.875rem;">
                                        <a href="{{ route('freelancer.available-projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=240&h=90&fit=crop&auto=format" alt="Apps" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Dev. de Apps</div>
                                        </a>
                                        <a href="{{ route('freelancer.available-projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=240&h=90&fit=crop&auto=format" alt="Data" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Entrada de Dados</div>
                                        </a>
                                        <a href="{{ route('freelancer.available-projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=240&h=90&fit=crop&auto=format" alt="Local" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Trabalhos Locais</div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Painel: concursos -->
                                <div x-show="tab==='concursos'" style="display:none;">
                                    <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Concursos activos</p>
                                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem;margin-bottom:.875rem;">
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=240&h=90&fit=crop&auto=format" alt="Video" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Edição de Vídeo</div>
                                        </a>
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?w=240&h=90&fit=crop&auto=format" alt="Escrita" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Escrita & Conteúdo</div>
                                        </a>
                                        <a href="{{ route('public.projects') }}" style="border-radius:.875rem;overflow:hidden;text-decoration:none;display:block;transition:transform .18s,box-shadow .18s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                                            <div style="height:82px;overflow:hidden;position:relative;"><img src="https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=240&h=90&fit=crop&auto=format" alt="Tech" style="width:100%;height:100%;object-fit:cover;display:block;"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.65) 0%,transparent 60%);"></div></div>
                                            <div style="background:#1e2a3a;padding:.45rem .7rem;font-size:.73rem;font-weight:700;color:#e2e8f0;">Tecnologia</div>
                                        </a>
                                    </div>
                                </div>
                                <!-- Footer links comuns -->
                                <div style="border-top:1px solid rgba(255,255,255,.07);padding-top:.75rem;margin-top:auto;display:flex;align-items:center;justify-content:space-between;">
                                    <span style="font-size:.75rem;font-weight:600;color:#94a3b8;">Outros trabalhos populares</span>
                                    <a href="{{ route('public.projects') }}" style="font-size:.75rem;font-weight:700;color:#0099d6;text-decoration:none;display:flex;align-items:center;gap:.25rem;">Ver mais <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg></a>
                                </div>
                                <div style="display:flex;flex-wrap:wrap;gap:.35rem .875rem;margin-top:.5rem;">
                                    <a href="{{ route('public.projects') }}" style="font-size:.73rem;color:#64748b;text-decoration:none;" onmouseover="this.style.color='#0099d6'" onmouseout="this.style.color='#64748b'">Desenvolvimento de Software</a>
                                    <a href="{{ route('public.projects') }}" style="font-size:.73rem;color:#64748b;text-decoration:none;" onmouseover="this.style.color='#0099d6'" onmouseout="this.style.color='#64748b'">Escrita & Conteúdo</a>
                                    <a href="{{ route('public.projects') }}" style="font-size:.73rem;color:#64748b;text-decoration:none;" onmouseover="this.style.color='#0099d6'" onmouseout="this.style.color='#64748b'">Edição de Vídeo</a>
                                    <a href="{{ route('public.projects') }}" style="font-size:.73rem;color:#64748b;text-decoration:none;" onmouseover="this.style.color='#0099d6'" onmouseout="this.style.color='#64748b'">SEO & SEM</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-data="{open:false}" class="relative">
                    <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:0.35rem;white-space:nowrap;">
                        Soluções
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute left-0 mt-2 rounded-2xl z-50"
                         style="width:560px;max-width:calc(100vw - 2rem);background:#141928;box-shadow:0 24px 64px rgba(0,0,0,.5);border:1px solid rgba(255,255,255,.08);padding:1.25rem;">
                        <p style="font-size:.68rem;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:1px;margin:0 0 .875rem .25rem;">Para o seu negócio</p>
                        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:.625rem;">
                            <a href="{{ route('sobre.investidores') }}"
                               style="display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 1rem;border-radius:.875rem;text-decoration:none;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);transition:background .15s,border-color .15s;"
                               onmouseover="this.style.background='rgba(0,153,214,.1)';this.style.borderColor='rgba(0,153,214,.3)'" onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.06)'">
                                <svg width="22" height="22" fill="none" stroke="#0099d6" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.875rem;">Empresas</span>
                                    <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.2rem;line-height:1.5;">Potencialize a sua vantagem competitiva com a 24 Horas.</span>
                                </span>
                            </a>
                            <a href="{{ route('sobre.sobre-nos') }}"
                               style="display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 1rem;border-radius:.875rem;text-decoration:none;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);transition:background .15s,border-color .15s;"
                               onmouseover="this.style.background='rgba(0,153,214,.1)';this.style.borderColor='rgba(0,153,214,.3)'" onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.06)'">
                                <svg width="22" height="22" fill="none" stroke="#0099d6" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.875rem;">Desafios de Inovação</span>
                                    <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.2rem;line-height:1.5;">Transforme desafios em soluções com a nossa rede.</span>
                                </span>
                            </a>
                            <a href="{{ route('sobre.como-funciona') }}"
                               style="display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 1rem;border-radius:.875rem;text-decoration:none;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);transition:background .15s,border-color .15s;"
                               onmouseover="this.style.background='rgba(0,153,214,.1)';this.style.borderColor='rgba(0,153,214,.3)'" onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.06)'">
                                <svg width="22" height="22" fill="none" stroke="#0099d6" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.875rem;">Serviços de campo</span>
                                    <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.2rem;line-height:1.5;">Entregue expertise em qualquer lugar, sob demanda.</span>
                                </span>
                            </a>
                            <a href="{{ route('sobre.como-funciona') }}"
                               style="display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 1rem;border-radius:.875rem;text-decoration:none;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);transition:background .15s,border-color .15s;"
                               onmouseover="this.style.background='rgba(0,153,214,.1)';this.style.borderColor='rgba(0,153,214,.3)'" onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.06)'">
                                <svg width="22" height="22" fill="none" stroke="#0099d6" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.875rem;">API da Plataforma</span>
                                    <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.2rem;line-height:1.5;">Integre a nossa workforce na sua aplicação.</span>
                                </span>
                            </a>
                            <a href="{{ route('sobre.investidores') }}"
                               style="display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 1rem;border-radius:.875rem;text-decoration:none;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);transition:background .15s,border-color .15s;"
                               onmouseover="this.style.background='rgba(0,153,214,.1)';this.style.borderColor='rgba(0,153,214,.3)'" onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.06)'">
                                <svg width="22" height="22" fill="none" stroke="#0099d6" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4"/></svg>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.875rem;">IA para negócios</span>
                                    <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.2rem;line-height:1.5;">Profissionais de IA para transformar o seu negócio.</span>
                                </span>
                            </a>
                            <a href="{{ route('freelancers.search') }}"
                               style="display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 1rem;border-radius:.875rem;text-decoration:none;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);transition:background .15s,border-color .15s;"
                               onmouseover="this.style.background='rgba(0,153,214,.1)';this.style.borderColor='rgba(0,153,214,.3)'" onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.06)'">
                                <svg width="22" height="22" fill="none" stroke="#0099d6" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.875rem;">Trabalhos locais</span>
                                    <span style="display:block;font-size:.75rem;color:#94a3b8;margin-top:.2rem;line-height:1.5;">Encontre ajuda perto de si, em qualquer lugar.</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
            @endguest
        </div>

        <!-- Direita: Botões -->
        <div class="header-actions">
            @guest
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link">Registo</a>
                <a href="{{ route('client.projects') }}" class="ml-2 px-4 py-2 rounded-lg bg-[#ff2d55] text-white font-bold shadow hover:bg-[#e60039] transition hp-btn-pulse">Publicar projecto</a>
            @else
                {{-- ── BARRA AUTENTICADA ────────────────────────────────────── --}}
                <div style="display:flex;align-items:center;gap:.625rem;">
                    {{-- Ícone Mensagens + sino: apenas para não-admin --}}
                    @if(auth()->user()->activeRole() !== 'admin')
                    <a href="{{ auth()->user()->activeRole() === 'freelancer' ? route('freelancer.dashboard') : route('client.dashboard') }}"
                       title="Mensagens"
                       style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);color:#e2e8f0;transition:background .15s,border-color .15s;text-decoration:none;"
                       onmouseover="this.style.background='rgba(0,186,255,.13)';this.style.borderColor='rgba(0,186,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.08)'">
                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </a>
                    <livewire:notification-bell />
                    @endif
                    {{-- Publicar / Admin Menu --}}
                    @if(auth()->user()->activeRole() === 'admin')
                    {{-- Notificação dropdown (admin) --}}
                    <div x-data="{open:false}" class="relative">
                        <button @click="open = !open"
                                style="display:flex;align-items:center;gap:.4rem;padding:.45rem .95rem;border-radius:.6875rem;background:#ff2d55;color:#fff;font-weight:700;font-size:.84rem;border:none;cursor:pointer;white-space:nowrap;transition:background .15s;"
                                onmouseover="this.style.background='#e60039'" onmouseout="this.style.background='#ff2d55'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Notificação
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" :class="open?'rotate-180':''" style="transition:transform .15s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 rounded-xl z-50"
                             style="width:230px;background:#141928;box-shadow:0 16px 48px rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.08);padding:.625rem;">
                            <a href="{{ route('admin.notifications.mass', ['target' => 'all']) }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(255,45,85,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:32px;height:32px;border-radius:8px;background:rgba(255,45,85,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="15" height="15" fill="none" stroke="#ff2d55" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <span style="font-weight:700;color:#f1f5f9;font-size:.82rem;">Todos Activos</span>
                            </a>
                            <a href="{{ route('admin.notifications.mass', ['target' => 'freelancer']) }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(0,186,255,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:32px;height:32px;border-radius:8px;background:rgba(0,186,255,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="15" height="15" fill="none" stroke="#00baff" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                                </span>
                                <span style="font-weight:700;color:#f1f5f9;font-size:.82rem;">Apenas Freelancers</span>
                            </a>
                            <a href="{{ route('admin.notifications.mass', ['target' => 'cliente']) }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(16,185,129,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:32px;height:32px;border-radius:8px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="15" height="15" fill="none" stroke="#10b981" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <span style="font-weight:700;color:#f1f5f9;font-size:.82rem;">Apenas Clientes</span>
                            </a>
                            <a href="{{ route('admin.notifications.mass', ['target' => 'individual']) }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(251,191,36,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:32px;height:32px;border-radius:8px;background:rgba(251,191,36,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="15" height="15" fill="none" stroke="#fbbf24" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <span style="font-weight:700;color:#f1f5f9;font-size:.82rem;">Individual</span>
                            </a>
                        </div>
                    </div>
                    {{-- Menu Admin dropdown --}}
                    <div x-data="{open:false}" class="relative">
                        <button @click="open = !open"
                                style="display:flex;align-items:center;gap:.4rem;padding:.45rem .95rem;border-radius:.6875rem;background:#ff2d55;color:#fff;font-weight:700;font-size:.84rem;border:none;cursor:pointer;white-space:nowrap;transition:background .15s;"
                                onmouseover="this.style.background='#e60039'" onmouseout="this.style.background='#ff2d55'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Menu Admin
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" :class="open?'rotate-180':''" style="transition:transform .15s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 rounded-xl z-50"
                             style="width:260px;background:#141928;box-shadow:0 16px 48px rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.08);padding:.625rem;">
                            {{-- Gestão de Utilizadores --}}
                            <a href="{{ route('admin.users') }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(255,45,85,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(255,45,85,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#ff2d55" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Gestão de Utilizadores</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Utilizadores, serviços, comercial</span>
                                </span>
                            </a>
                            {{-- Financeiro --}}
                            <a href="{{ route('admin.financial') }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(255,45,85,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#10b981" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Financeiro</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Pagamentos, comissões, saques</span>
                                </span>
                            </a>
                            {{-- Suporte --}}
                            <a href="{{ route('admin.disputes') }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(255,45,85,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(0,186,255,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#00baff" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Suporte</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Disputas, notificações, loja, social</span>
                                </span>
                            </a>
                            {{-- Configurações --}}
                            <a href="{{ route('admin.settings') }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(255,45,85,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(251,191,36,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#fbbf24" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Configurações</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Taxas, categorias, definições gerais</span>
                                </span>
                            </a>
                            {{-- Sistema --}}
                            <a href="{{ route('admin.audit') }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(255,45,85,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(148,163,184,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Sistema</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Logs e auditoria</span>
                                </span>
                            </a>
                            {{-- Administradores (Master only) --}}
                            @if(in_array(optional(auth()->user())->admin_role, ['master', null]))
                            <a href="{{ route('admin.managers') }}" @click="open=false"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(255,45,85,.1)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(139,92,246,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#8b5cf6" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Administradores</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Cadastro e permissões de admins</span>
                                </span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @else
                    {{-- Não-admin: Publicar Projecto + Conteúdo --}}
                    <div x-data="{open:false, role:'{{ auth()->user()->activeRole() }}'}" class="relative">
                        <button @click="open = !open"
                                style="display:flex;align-items:center;gap:.4rem;padding:.45rem .95rem;border-radius:.6875rem;background:#ff2d55;color:#fff;font-weight:700;font-size:.84rem;border:none;cursor:pointer;white-space:nowrap;transition:background .15s;"
                                onmouseover="this.style.background='#e60039'" onmouseout="this.style.background='#ff2d55'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                            Publicar
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" :class="open?'rotate-180':''" style="transition:transform .15s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 rounded-xl z-50"
                             style="width:230px;background:#141928;box-shadow:0 16px 48px rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.08);padding:.625rem;">
                            <a href="{{ route('client.projects') }}" @click="open=false; if(role!=='cliente'){$event.preventDefault();$dispatch('open-role-switch-modal',{action:'projeto'})}"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(0,186,255,.08)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(255,45,85,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#ff2d55" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Projecto</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Publique um novo projecto</span>
                                </span>
                            </a>
                            <a href="{{ route('social.create') }}" @click="open=false; if(!['freelancer','creator'].includes(role)){$event.preventDefault();$dispatch('open-role-switch-modal',{action:'conteudo'})}"
                               style="display:flex;align-items:center;gap:.75rem;padding:.65rem .875rem;border-radius:.625rem;text-decoration:none;transition:background .15s;"
                               onmouseover="this.style.background='rgba(0,186,255,.08)'" onmouseout="this.style.background='transparent'">
                                <span style="width:34px;height:34px;border-radius:8px;background:rgba(0,186,255,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#00baff" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                </span>
                                <span>
                                    <span style="display:block;font-weight:700;color:#f1f5f9;font-size:.82rem;">Conteúdo</span>
                                    <span style="display:block;font-size:.72rem;color:#94a3b8;margin-top:.1rem;">Publique um post ou artigo</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    @endif
                    {{-- Troca de módulo (apenas para não-admins) --}}
                    @if(auth()->user()->activeRole() !== 'admin' && auth()->user()->canSwitchRole())
                        <form method="POST" action="{{ route('switch.role') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    style="display:flex;align-items:center;gap:.4rem;padding:.45rem .875rem;border-radius:.6875rem;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#e2e8f0;font-size:.8rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:background .15s,border-color .15s;"
                                    onmouseover="this.style.background='rgba(0,186,255,.12)';this.style.borderColor='rgba(0,186,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.1)'"
                                    title="Mudar para modo {{ auth()->user()->activeRole() === 'freelancer' ? 'Cliente' : 'Freelancer' }}">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                {{ auth()->user()->activeRole() === 'freelancer' ? 'Modo Cliente' : 'Modo Freelancer' }}
                            </button>
                        </form>
                    @endif
                    {{-- Avatar / Menu do utilizador --}}
                    <div x-data="{open:false}" class="relative">
                        <button @click="open = !open"
                                style="display:flex;align-items:center;gap:.5rem;padding:.3rem .5rem .3rem .3rem;border-radius:2rem;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='rgba(255,255,255,.06)'">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="avatar-sm" onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.svg') }}';">
                            <span style="font-size:.82rem;font-weight:600;color:#e2e8f0;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</span>
                            <svg width="13" height="13" fill="none" stroke="#94a3b8" stroke-width="2.5" viewBox="0 0 24 24" :class="open?'rotate-180':''" style="transition:transform .15s;margin-right:.25rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-52 rounded-xl py-2 shadow-xl"
                             style="background:#141928;border:1px solid rgba(255,255,255,.08);">
                            <div style="padding:.5rem 1rem .375rem;border-bottom:1px solid rgba(255,255,255,.07);margin-bottom:.375rem;">
                                <p style="font-size:.8rem;font-weight:700;color:#f1f5f9;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</p>
                                <p style="font-size:.7rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->email }}</p>
                            </div>
                            @if(in_array(auth()->user()->activeRole(), ['cliente','client']))
                                <a href="{{ route('client.dashboard') }}" class="block px-4 py-2 hover:bg-white/5" style="font-size:.8rem;color:#e2e8f0;">Dashboard</a>
                            @elseif(auth()->user()->activeRole() === 'freelancer')
                                <a href="{{ route('freelancer.dashboard') }}" class="block px-4 py-2 hover:bg-white/5" style="font-size:.8rem;color:#e2e8f0;">Dashboard</a>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-white/5" style="font-size:.8rem;color:#e2e8f0;">Dashboard</a>
                            @endif
                            @if(auth()->user()->activeRole() !== 'admin')
                            <a href="{{ auth()->user()->activeRole() === 'freelancer' ? route('freelancer.notifications') : route('notifications') }}" class="block px-4 py-2 hover:bg-white/5" style="font-size:.8rem;color:#e2e8f0;">Notificações</a>
                            @endif
                            <div style="border-top:1px solid rgba(255,255,255,.07);margin:.375rem 0;"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-white/5" style="font-size:.8rem;color:#f87171;">Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest
        </div>

        <div class="mobile-nav flex items-center gap-3">
            @guest
                <a href="/register" class="mobile-cta btn-primary">Registo</a>
            @else
                @if(auth()->user()->activeRole() !== 'admin')
                    <livewire:notification-bell />
                @endif
            @endguest
            @auth
            {{-- Utilizador autenticado: toggle sidebar via Alpine.store --}}
            <button @click="$store.sidebar.toggle()" class="p-2 rounded-md text-white bg-[#00baff]/20 border border-white/20 hover:bg-[#00baff]/30 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            @else
            {{-- Visitante: abre dropdown do menu público --}}
            <button @click="open = !open" class="p-2 rounded-md text-white bg-[#00baff]/20 border border-white/20 hover:bg-[#00baff]/30 transition">
                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            @endauth
        </div>
    </div>

    <div x-show="open" x-transition class="px-4 pb-4 md:hidden">
        <div class="mobile-menu-dropdown flex flex-col gap-1 bg-[#071422] border border-white/10 rounded-xl p-3 mt-2 shadow-xl">
            @guest
            <!-- Accordion: Contratar -->
            <div x-data="{sub:false}">
                <button @click="sub=!sub" class="nav-link w-full text-left flex items-center justify-between">
                    <span>Contratar</span>
                    <svg :class="sub ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="sub" x-cloak class="pl-3 mt-1 flex flex-col gap-1 border-l border-white/10 ml-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2 pt-1">Por habilidade</p>
                    <a href="{{ route('freelancers.search', ['skill' => 'design']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>Designers Gráficos</a>
                    <a href="{{ route('freelancers.search', ['skill' => 'web']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M9 21V9"/></svg>Dev. de Websites</a>
                    <a href="{{ route('freelancers.search', ['skill' => 'mobile']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><rect x="7" y="2" width="10" height="20" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01"/></svg>Apps Mobile</a>
                    <a href="{{ route('freelancers.search', ['skill' => 'video']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>Edição de Vídeo</a>
                    <a href="{{ route('freelancers.search', ['skill' => 'marketing']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 20V10M12 20V4M6 20v-6"/></svg>Marketing Digital</a>
                    <a href="{{ route('freelancers.search', ['skill' => 'redacao']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Redação & Conteúdo</a>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2 pt-2">Por localização</p>
                    <a href="{{ route('freelancers.search', ['location' => 'luanda']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Luanda</a>
                    <a href="{{ route('freelancers.search', ['location' => 'benguela']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Benguela</a>
                    <a href="{{ route('freelancers.search', ['location' => 'huambo']) }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Huambo</a>
                    <a href="{{ route('freelancers.search') }}" class="nav-link text-sm text-cyan-400 flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>Ver todos os freelancers</a>
                </div>
            </div>
            <!-- Accordion: Encontrar Trabalho -->
            <div x-data="{sub:false}">
                <button @click="sub=!sub" class="nav-link w-full text-left flex items-center justify-between">
                    <span>Encontrar Trabalho</span>
                    <svg :class="sub ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="sub" x-cloak class="pl-3 mt-1 flex flex-col gap-1 border-l border-white/10 ml-2">
                    <a href="{{ route('public.projects') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Projectos abertos</a>
                    <a href="{{ route('public.projects') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>Concursos activos</a>
                    <a href="{{ route('freelancers.search') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>Explorar categorias</a>
                    <a href="{{ route('public.projects') }}" class="nav-link text-sm text-cyan-400 flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>Ver mais trabalhos</a>
                </div>
            </div>
            <!-- Accordion: Soluções -->
            <div x-data="{sub:false}">
                <button @click="sub=!sub" class="nav-link w-full text-left flex items-center justify-between">
                    <span>Soluções</span>
                    <svg :class="sub ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="sub" x-cloak class="pl-3 mt-1 flex flex-col gap-1 border-l border-white/10 ml-2">
                    <a href="{{ route('sobre.investidores') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>Empresas</a>
                    <a href="{{ route('sobre.sobre-nos') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>Desafios de Inovação</a>
                    <a href="{{ route('sobre.como-funciona') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>Serviços de campo</a>
                    <a href="{{ route('sobre.como-funciona') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>API da Plataforma</a>
                    <a href="{{ route('sobre.investidores') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4"/></svg>IA para negócios</a>
                    <a href="{{ route('freelancers.search') }}" class="nav-link text-sm flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="#0099d6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Trabalhos locais</a>
                </div>
            </div>
            <div class="border-t border-white/10 my-1"></div>
            <a href="#depoimentos" class="nav-link">Depoimentos</a>
            @endguest
            @guest
                <a href="/login" class="nav-link">Login</a>
                <a href="/register" class="nav-link btn-primary">Registo</a>
            @else
                {{-- Card do utilizador --}}
                <div class="flex items-center gap-3 px-2 py-2 border border-white/10 rounded-xl mb-1">
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="avatar-sm" onerror="this.onerror=null;this.src='{{ asset('img/default-avatar.svg') }}'">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-white text-sm truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                {{-- Dashboard --}}
                @if(in_array(auth()->user()->activeRole(), ['cliente','client']))
                    <a href="{{ route('client.dashboard') }}" class="nav-link">Dashboard</a>
                @elseif(auth()->user()->activeRole() === 'freelancer')
                    <a href="{{ route('freelancer.dashboard') }}" class="nav-link">Dashboard</a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                @endif
                {{-- Publicar / Notificação --}}
                <div class="border-t border-white/10 my-1"></div>
                @if(auth()->user()->activeRole() === 'admin')
                <div>
                <p class="text-xs font-bold uppercase tracking-wider px-2 pb-1" style="color:#ff2d55;">Notificação</p>
                <a href="{{ route('admin.notifications.mass', ['target' => 'all']) }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#ff2d55" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Todos Activos
                </a>
                <a href="{{ route('admin.notifications.mass', ['target' => 'freelancer']) }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#00baff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                    Apenas Freelancers
                </a>
                <a href="{{ route('admin.notifications.mass', ['target' => 'cliente']) }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Apenas Clientes
                </a>
                <a href="{{ route('admin.notifications.mass', ['target' => 'individual']) }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#fbbf24" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Individual
                </a>
                <div class="border-t border-white/10 my-1"></div>
                <p class="text-xs font-bold uppercase tracking-wider px-2 pb-1 pt-1" style="color:#ff2d55;">Menu Admin</p>
                <a href="{{ route('admin.users') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#ff2d55" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Gestão de Utilizadores
                </a>
                <a href="{{ route('admin.financial') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Financeiro
                </a>
                <a href="{{ route('admin.disputes') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#00baff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Suporte
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#fbbf24" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                    Configurações
                </a>
                <a href="{{ route('admin.audit') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Sistema
                </a>
                @if(in_array(optional(auth()->user())->admin_role, ['master', null]))
                <a href="{{ route('admin.managers') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#8b5cf6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                    Administradores
                </a>
                @endif
                </div>
                @else
                <div x-data="{role:'{{ auth()->user()->activeRole() }}'}">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2 pb-1">Publicar</p>
                <a href="{{ route('client.projects') }}" @click="if(role!=='cliente'){$event.preventDefault();$dispatch('open-role-switch-modal',{action:'projeto'})}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#ff2d55" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Projecto
                </a>
                <a href="{{ route('social.create') }}" @click="if(!['freelancer','creator'].includes(role)){$event.preventDefault();$dispatch('open-role-switch-modal',{action:'conteudo'})}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#00baff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                    Conteúdo
                </a>
                </div>
                @endif
                {{-- Troca de módulo --}}
                @if(auth()->user()->canSwitchRole())
                    <div class="border-t border-white/10 my-1"></div>
                    <form method="POST" action="{{ route('switch.role') }}">
                        @csrf
                        <button type="submit" class="nav-link text-left w-full flex items-center gap-2 text-cyan-400">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            {{ auth()->user()->activeRole() === 'freelancer' ? 'Mudar para Modo Cliente' : 'Mudar para Modo Freelancer' }}
                        </button>
                    </form>
                @endif
                <div class="border-t border-white/10 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-left w-full text-red-400">Sair</button>
                </form>
            @endguest
        </div>
    </div>
{{-- ── Modal: Troca de Modo para Publicar ────────────────────────────── --}}
@auth
@php
    $currentRoleLabel = match(auth()->user()->activeRole()) {
        'freelancer' => 'Freelancer',
        'creator'    => 'Criador',
        default      => 'Cliente',
    };
@endphp
<div x-data="{
    show: false,
    action: '',
    get title() { return this.action==='projeto' ? 'Modo Cliente necessário' : 'Modo Freelancer necessário' },
    get desc()  { return this.action==='projeto'
        ? 'Para publicar um projecto e contratar freelancers, precisa de estar no <strong style=\'color:#fff\'>Modo Cliente</strong>. A troca é instantânea — pode voltar ao modo anterior a qualquer momento.'
        : 'Para publicar conteúdo, posts e artigos, precisa de estar no <strong style=\'color:#fff\'>Modo Freelancer</strong>. A troca é instantânea — pode voltar ao modo anterior a qualquer momento.' },
    get targetMode() { return this.action==='projeto' ? 'Cliente' : 'Freelancer' }
}"
    @open-role-switch-modal.window="show=true; action=$event.detail.action"
    @keydown.escape.window="show=false"
    x-show="show"
    x-cloak
    @click.self="show=false"
    style="position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(2,10,18,.75);backdrop-filter:blur(8px);">

    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-3"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="position:relative;background:linear-gradient(160deg,#0f1a2e 0%,#0a1628 100%);border:1px solid rgba(255,255,255,.1);border-radius:1.25rem;padding:2rem;max-width:420px;width:100%;box-shadow:0 32px 80px rgba(0,0,0,.6);">

        {{-- Close --}}
        <button @click="show=false"
                style="position:absolute;top:.875rem;right:.875rem;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#94a3b8;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Icon --}}
        <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,rgba(0,186,255,.18),rgba(0,186,255,.04));border:1px solid rgba(0,186,255,.22);display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
            <template x-if="action==='projeto'">
                <svg width="26" height="26" fill="none" stroke="#00baff" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M12 11h.01M12 15h.01"/></svg>
            </template>
            <template x-if="action==='conteudo'">
                <svg width="26" height="26" fill="none" stroke="#00baff" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
            </template>
        </div>

        {{-- Title --}}
        <h3 x-text="title" style="font-size:1.2rem;font-weight:800;color:#f1f5f9;margin:0 0 .5rem;line-height:1.3;"></h3>

        {{-- Description --}}
        <p x-html="desc" style="font-size:.875rem;color:#94a3b8;line-height:1.7;margin:0 0 1.375rem;"></p>

        {{-- Current → Target Mode indicator --}}
        <div style="display:flex;align-items:center;justify-content:center;gap:.875rem;margin-bottom:1.375rem;padding:.8rem 1rem;background:rgba(255,255,255,.035);border-radius:.875rem;border:1px solid rgba(255,255,255,.07);">
            <div style="text-align:center;">
                <div style="font-size:.6rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.3rem;">Modo actual</div>
                <span style="font-size:.8rem;font-weight:700;color:#fca5a5;background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.2);border-radius:.45rem;padding:.18rem .6rem;">{{ $currentRoleLabel }}</span>
            </div>
            <svg width="18" height="18" fill="none" stroke="#334155" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            <div style="text-align:center;">
                <div style="font-size:.6rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.3rem;">Modo necessário</div>
                <span x-text="targetMode" style="font-size:.8rem;font-weight:700;color:#86efac;background:rgba(0,186,255,.1);border:1px solid rgba(0,186,255,.22);border-radius:.45rem;padding:.18rem .6rem;"></span>
            </div>
        </div>

        {{-- Buttons --}}
        @if(auth()->user()->canSwitchRole())
        <form method="POST" action="{{ route('switch.role') }}" style="display:flex;flex-direction:column;gap:.5rem;">
            @csrf
            <button type="submit"
                    style="width:100%;padding:.72rem 1rem;border-radius:.75rem;background:linear-gradient(135deg,#00baff 0%,#0091cc 100%);color:#021018;font-weight:800;font-size:.9rem;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;transition:opacity .15s;box-shadow:0 4px 18px rgba(0,186,255,.25);"
                    onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Mudar de Modo agora
            </button>
            <button type="button" @click="show=false"
                    style="width:100%;padding:.6rem 1rem;border-radius:.75rem;background:transparent;color:#64748b;font-weight:600;font-size:.85rem;border:1px solid rgba(255,255,255,.07);cursor:pointer;transition:all .15s;"
                    onmouseover="this.style.background='rgba(255,255,255,.05)';this.style.color='#94a3b8'" onmouseout="this.style.background='transparent';this.style.color='#64748b'">
                Cancelar
            </button>
        </form>
        @endif
    </div>
</div>
@endauth
</header>
