<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function findById(int $id): ?Service
    {
        return Service::find($id);
    }

    public function all(): Collection
    {
        return Service::all();
    }

    public function create(array $data): Service
    {
        return Service::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) Service::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return (bool) Service::destroy($id);
    }

    public function getClientServices(int $clientId): Collection
    {
        return Service::where('cliente_id', $clientId)->orderByDesc('created_at')->get();
    }

    public function getFreelancerServices(int $freelancerId): Collection
    {
        return Service::where('freelancer_id', $freelancerId)->orderByDesc('created_at')->get();
    }

    public function findWithCandidates(int $id): ?Service
    {
        return Service::with('candidates')->find($id);
    }

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Service::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['cliente_id'])) {
            $query->where('cliente_id', $filters['cliente_id']);
        }

        if (!empty($filters['freelancer_id'])) {
            $query->where('freelancer_id', $filters['freelancer_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('titulo', 'ilike', "%{$filters['search']}%");
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }
}
