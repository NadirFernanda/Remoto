<?php

namespace App\Repositories\Contracts;

use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ServiceRepositoryInterface extends RepositoryInterface
{
    /** @return Collection<int, Service> */
    public function getClientServices(int $clientId): Collection;

    /** @return Collection<int, Service> */
    public function getFreelancerServices(int $freelancerId): Collection;

    public function findWithCandidates(int $id): ?Service;

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator;
}
