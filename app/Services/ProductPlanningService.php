<?php

namespace App\Services;

use App\Repositories\ProductPlanningRepository;

class ProductPlanningService
{
    protected $repository;

    public function __construct(ProductPlanningRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    public function update($id, $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
