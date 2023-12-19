<?php

namespace App\Services;

use App\Repositories\OrderTypeRepository;

class OrderTypeService
{
    protected $repository;

    public function __construct(OrderTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }
}
