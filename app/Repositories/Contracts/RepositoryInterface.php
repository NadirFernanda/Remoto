<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    public function findById(int $id): mixed;
    public function all(): iterable;
    public function create(array $data): mixed;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
