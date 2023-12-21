<?php

namespace App\Services;

use App\Repositories\ProductLocationRepository;

class ProductLocationService
{
    protected $repository;

    public function __construct(ProductLocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }
}
