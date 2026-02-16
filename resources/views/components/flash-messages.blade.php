@php
    $hasErrors = isset($errors) && $errors->any();
    $success = session('status') ?? session('success');
    $error = session('error');
@endphp

@if($success || $error || $hasErrors)
    <div id="flash-container" class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-2xl px-4">
        @if($success)
            <div class="flash flash-success mb-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded shadow flex justify-between items-start">
                <div class="flex-1 mr-4">{{ $success }}</div>
                <button class="flash-close text-green-700 hover:text-green-900" aria-label="Fechar">✕</button>
            </div>
        @endif

        @if($error)
            <div class="flash flash-error mb-2 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded shadow flex justify-between items-start">
                <div class="flex-1 mr-4">{{ $error }}</div>
                <button class="flash-close text-red-700 hover:text-red-900" aria-label="Fechar">✕</button>
            </div>
        @endif

        @if($hasErrors)
            <div class="flash flash-error mb-2 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded shadow">
                <div class="font-semibold mb-1">Foram encontrados erros:</div>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <script>
        (function(){
            // Auto-hide after 6s and wire up close buttons
            setTimeout(function(){
                var c = document.getElementById('flash-container');
                if(c) c.style.display = 'none';
            }, 6000);
            document.addEventListener('click', function(e){
                if(e.target && e.target.classList && e.target.classList.contains('flash-close')){
                    var c = document.getElementById('flash-container');
                    if(c) c.style.display = 'none';
                }
            });
            // Allow Livewire to dispatch a 'flash' event with detail {type,message}
            document.addEventListener('livewire:load', function(){
                if(window.Livewire){
                    Livewire.on('flash', function(payload){
                        if(!payload) return;
                        // simple page reload to pick up session flash, or we could render via JS
                        location.reload();
                    });
                }
            });
        })();
    </script>
@endif
