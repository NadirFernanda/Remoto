<div class="p-6 bg-white rounded shadow max-w-3xl mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Minhas Avaliações</h2>
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-2">Avaliações feitas por você</h3>
        <table class="w-full text-left border mb-4">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-3">Para</th>
                    <th class="py-2 px-3">Nota</th>
                    <th class="py-2 px-3">Comentário</th>
                    <th class="py-2 px-3">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviewsGiven as $review)
                    <tr>
                        <td class="py-2 px-3">{{ $review->target->name ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $review->rating }}/5</td>
                        <td class="py-2 px-3">{{ $review->comment }}</td>
                        <td class="py-2 px-3">{{ $review->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-2 px-3 text-gray-500">Nenhuma avaliação feita.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>
        <h3 class="text-lg font-semibold mb-2">Avaliações recebidas</h3>
        <table class="w-full text-left border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-3">De</th>
                    <th class="py-2 px-3">Nota</th>
                    <th class="py-2 px-3">Comentário</th>
                    <th class="py-2 px-3">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviewsReceived as $review)
                    <tr>
                        <td class="py-2 px-3">{{ $review->author->name ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $review->rating }}/5</td>
                        <td class="py-2 px-3">{{ $review->comment }}</td>
                        <td class="py-2 px-3">{{ $review->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-2 px-3 text-gray-500">Nenhuma avaliação recebida.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
