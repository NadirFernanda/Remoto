<?php

namespace App\Services;

use App\Models\Service;

class ServiceStateService
{
    /**
     * Mapa de transições válidas: estado_actual => [estados_destino_permitidos]
     */
    private const TRANSITIONS = [
        'published'   => ['negotiating', 'accepted', 'cancelled', 'em_moderacao'],
        'negotiating' => ['accepted', 'published', 'cancelled'],
        'accepted'    => ['in_progress', 'cancelled'],
        'in_progress' => ['delivered', 'disputed', 'cancelled'],
        'delivered'   => ['completed', 'disputed', 'in_progress'],
        'completed'   => [],
        'disputed'    => ['in_progress', 'completed', 'cancelled'],
        'cancelled'   => [],
        'em_moderacao' => ['published', 'cancelled'],
    ];

    /**
     * Estados que permitem troca de mensagens no chat.
     */
    private const CHAT_ALLOWED = [
        'negotiating',
        'accepted',
        'in_progress',
        'delivered',
        'disputed',
    ];

    /**
     * Verifica se a transição do estado actual para $to é válida.
     */
    public function canTransition(Service $service, string $to): bool
    {
        $allowed = self::TRANSITIONS[$service->status] ?? [];
        return in_array($to, $allowed, true);
    }

    /**
     * Retorna os estados de destino válidos a partir do estado actual.
     *
     * @return string[]
     */
    public function validNextStatuses(Service $service): array
    {
        return self::TRANSITIONS[$service->status] ?? [];
    }

    /**
     * Indica se o serviço permite envio de mensagens no chat.
     */
    public function allowsChat(Service $service): bool
    {
        return in_array($service->status, self::CHAT_ALLOWED, true);
    }

    /**
     * Indica se o cliente pode solicitar reembolso neste estado.
     */
    public function canRequestRefund(Service $service): bool
    {
        return !$service->is_payment_released
            && in_array($service->status, ['published', 'negotiating', 'accepted', 'in_progress', 'delivered'], true);
    }

    /**
     * Indica se o serviço pode ser disputado (abrir disputa).
     */
    public function canDispute(Service $service): bool
    {
        return in_array($service->status, ['in_progress', 'delivered'], true);
    }
}
