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
}
