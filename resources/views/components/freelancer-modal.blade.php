<!-- Modal de Cadastro Freelancer -->
<div id="freelancer-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative">
        <button onclick="closeFreelancerModal()" class="absolute top-2 right-2 text-gray-500 hover:text-cyan-600 text-2xl">&times;</button>
        <h2 class="text-2xl font-bold text-cyan-600 mb-4 text-center">Cadastro Rápido Freelancer</h2>
        <form method="POST" action="/register">
            @csrf
            <input type="hidden" name="role" value="freelancer">
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1" for="name">Nome</label>
                <input type="text" name="name" id="name" required class="w-full px-3 py-2 border rounded focus:ring-cyan-400">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1" for="email">E-mail</label>
                <input type="email" name="email" id="email" required class="w-full px-3 py-2 border rounded focus:ring-cyan-400">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1" for="password">Senha</label>
                <input type="password" name="password" id="password" required class="w-full px-3 py-2 border rounded focus:ring-cyan-400">
            </div>
            <button type="submit" class="w-full bg-cyan-500 text-white font-bold py-2 rounded hover:bg-cyan-600 transition">Cadastrar</button>
        </form>
    </div>
</div>
<script>
    function openFreelancerModal() {
        document.getElementById('freelancer-modal').style.display = 'flex';
    }
    function closeFreelancerModal() {
        document.getElementById('freelancer-modal').style.display = 'none';
    }
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.querySelector('a[href="/register?freelancer=1"]');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openFreelancerModal();
            });
        }
    });
</script>
