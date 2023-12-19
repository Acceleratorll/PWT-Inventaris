<?php

namespace App\Services;

use App\Repositories\ProductTransactionLocationRepository;

class ProductTransactionLocationService
{
    protected $repository;

    public function __construct(ProductTransactionLocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }
}
