<?php

namespace App\Services;

use App\Repositories\ProductTransactionRepository;

class ProductTransactionService
{
    protected $repository;

    public function __construct(ProductTransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }
}
