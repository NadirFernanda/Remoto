<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Service;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| chat.{serviceId}  — Private channel per service chat thread.
|   Authorized for: service owner (cliente), contracted freelancer,
|   or any candidate with an active proposal.
|
*/

Broadcast::channel('chat.{serviceId}', function ($user, int $serviceId) {
    $service = Service::find($serviceId);

    if (!$service) {
        return false;
    }

    $isOwner     = $user->id === $service->cliente_id;
    $isFreelancer = $user->id === $service->freelancer_id;
    $isCandidate  = $service->candidates()
        ->where('freelancer_id', $user->id)
        ->whereIn('status', ['pending', 'proposal_sent', 'invited', 'chosen'])
        ->exists();

    return $isOwner || $isFreelancer || $isCandidate;
});
