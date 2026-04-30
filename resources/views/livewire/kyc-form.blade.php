<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#00baff] to-[#0095cc] rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-extrabold">Verificação de Identidade (KYC)</h2>
        <p class="text-sm text-white/75 mt-1">Envie os seus documentos para activar todas as funcionalidades da plataforma</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

    {{-- Status actual --}}
    @php
        $statusMap = [
            'pending'  => ['label' => 'Pendente',  'class' => 'bg-yellow-100 text-yellow-700'],
            'verified' => ['label' => 'Verificado', 'class' => 'bg-green-100 text-green-700'],
            'approved' => ['label' => 'Aprovado',  'class' => 'bg-green-100 text-green-700'],
            'rejected' => ['label' => 'Rejeitado', 'class' => 'bg-red-100 text-red-600'],
        ];
        $currentKyc = auth()->user()->kyc_status ?? 'pending';
        $kycInfo = $statusMap[$currentKyc] ?? $statusMap['pending'];
    @endphp

    <div class="mb-6 flex items-center gap-3">
        <span class="text-sm font-medium text-gray-500">Estado actual:</span>
        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $kycInfo['class'] }}">
            {{ $kycInfo['label'] }}
        </span>
    </div>

    {{-- Submission already approved --}}
    @if($currentKyc === 'verified' || ($existing && $existing->status === 'approved'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 font-medium">
            ✓ A sua identidade foi verificada com sucesso. Não é necessário enviar novamente.
        </div>

    {{-- Pending submission --}}
    @elseif($existing && $existing->status === 'pending')
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
            <p class="font-semibold mb-1">⏳ Submissão em análise</p>
            <p class="text-sm">Os seus documentos foram recebidos e estão a ser analisados pela equipa 24HORAS. Receberá uma notificação assim que a verificação estiver concluída.</p>
            <p class="text-xs text-yellow-600 mt-2">Submetido em: {{ $existing->created_at->format('d/m/Y H:i') }}</p>
        </div>

    @else
        {{-- Success message --}}
        @if($successMessage)
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg font-semibold text-sm">
                {{ $successMessage }}
            </div>
        @endif

        {{-- Rejected: show reason --}}
        @if($existing && $existing->status === 'rejected')
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                <p class="font-semibold mb-1">✗ Submissão anterior rejeitada</p>
                @if($existing->admin_notes)
                    <p class="text-sm">Motivo: {{ $existing->admin_notes }}</p>
                @endif
                <p class="text-sm mt-1">Por favor, corrija os documentos e envie novamente.</p>
            </div>
        @endif

        {{-- Form --}}
        <form wire:submit.prevent="submit" enctype="multipart/form-data">
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de documento <span class="text-red-500">*</span></label>
                <select wire:model="documentType" class="block w-full max-w-xs rounded-lg border border-gray-200 py-2 px-3">
                    <option value="bi">Bilhete de Identidade (BI)</option>
                    <option value="passport">Passaporte</option>
                    <option value="driving_license">Carta de condução</option>
                </select>
                @error('documentType') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Frente do documento <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ nome: 'Nenhum ficheiro selecionado' }" class="flex items-center gap-3">
                        <label class="cursor-pointer inline-flex items-center gap-2 bg-cyan-50 text-cyan-700 font-semibold px-4 py-2 rounded-lg hover:bg-cyan-100 transition text-sm whitespace-nowrap">
                            Escolher ficheiro
                            <input type="file" wire:model="documentFront" accept="image/*,.pdf" class="hidden"
                                   @change="nome = $event.target.files[0]?.name ?? 'Nenhum ficheiro selecionado'">
                        </label>
                        <span class="text-sm text-gray-500 truncate max-w-[180px]" x-text="nome"></span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG ou PDF · máx. 10MB</p>
                    @error('documentFront') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                    @if($documentFront)
                        <p class="text-xs text-green-600 mt-1">✓ {{ $documentFront->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Verso do documento <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ nome: 'Nenhum ficheiro selecionado' }" class="flex items-center gap-3">
                        <label class="cursor-pointer inline-flex items-center gap-2 bg-cyan-50 text-cyan-700 font-semibold px-4 py-2 rounded-lg hover:bg-cyan-100 transition text-sm whitespace-nowrap">
                            Escolher ficheiro
                            <input type="file" wire:model="documentBack" accept="image/*,.pdf" class="hidden"
                                   @change="nome = $event.target.files[0]?.name ?? 'Nenhum ficheiro selecionado'">
                        </label>
                        <span class="text-sm text-gray-500 truncate max-w-[180px]" x-text="nome"></span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG ou PDF · máx. 10MB</p>
                    @error('documentBack') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                    @if($documentBack)
                        <p class="text-xs text-green-600 mt-1">✓ {{ $documentBack->getClientOriginalName() }}</p>
                    @endif
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Selfie com o documento <span class="text-gray-400">(opcional mas recomendado)</span>
                </label>
                <div x-data="{ nome: 'Nenhum ficheiro selecionado' }" class="flex items-center gap-3">
                    <label class="cursor-pointer inline-flex items-center gap-2 bg-cyan-50 text-cyan-700 font-semibold px-4 py-2 rounded-lg hover:bg-cyan-100 transition text-sm whitespace-nowrap">
                        Escolher ficheiro
                        <input type="file" wire:model="selfie" accept="image/*" class="hidden"
                               @change="nome = $event.target.files[0]?.name ?? 'Nenhum ficheiro selecionado'">
                    </label>
                    <span class="text-sm text-gray-500 truncate max-w-[180px]" x-text="nome"></span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Foto segurando o documento ao lado do rosto · JPG ou PNG · máx. 10MB</p>
                @error('selfie') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                @if($selfie)
                    <p class="text-xs text-green-600 mt-1">✓ {{ $selfie->getClientOriginalName() }}</p>
                @endif
            </div>

            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg text-blue-700 text-sm mb-6">
                <p class="font-semibold mb-1">🔒 Os seus documentos estão seguros</p>
                <p>Os ficheiros são armazenados de forma privada e apenas acessíveis pela equipa de verificação da 24HORAS. Não são partilhados com terceiros.</p>
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 bg-[#00baff] hover:bg-[#009ad6] text-white font-semibold px-6 py-2.5 rounded-lg transition disabled:opacity-60">
                <span wire:loading.remove>Enviar documentos para verificação</span>
                <span wire:loading>A enviar...</span>
            </button>
        </form>
    @endif

    </div>
</div>
