<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;

    /** @return Collection<int, User> */
    public function findByRole(string $role): Collection;

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator;
}
