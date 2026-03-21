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
                    {{-- Ícone Mensagens --}}
                    <a href="{{ in_array(auth()->user()->activeRole(), ['freelancer']) ? route('freelancer.dashboard') : route('client.dashboard') }}"
                       title="Mensagens"
                       style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);color:#e2e8f0;transition:background .15s,border-color .15s;text-decoration:none;"
                       onmouseover="this.style.background='rgba(0,186,255,.13)';this.style.borderColor='rgba(0,186,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.08)'">
                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </a>
                    {{-- Notificações --}}
                    <livewire:notification-bell />
                    {{-- Publicar: Projecto + Conteúdo --}}
                    <div x-data="{open:false}" class="relative">
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
                            <a href="{{ route('client.projects') }}" @click="open=false"
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
                            <a href="{{ route('social.create') }}" @click="open=false"
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
                    {{-- Troca de módulo --}}
                    @if(auth()->user()->canSwitchRole())
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
                            <a href="{{ route('freelancer.notifications') }}" class="block px-4 py-2 hover:bg-white/5" style="font-size:.8rem;color:#e2e8f0;">Notificações</a>
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
                <livewire:notification-bell />
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
                {{-- Publicar --}}
                <div class="border-t border-white/10 my-1"></div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2 pb-1">Publicar</p>
                <a href="{{ route('client.projects') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#ff2d55" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Projecto
                </a>
                <a href="{{ route('social.create') }}" class="nav-link flex items-center gap-2">
                    <svg width="14" height="14" fill="none" stroke="#00baff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                    Conteúdo
                </a>
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
</header>
