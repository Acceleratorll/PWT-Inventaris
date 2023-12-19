<?php

namespace App\Services;

use App\Repositories\LocationRepository;

class LocationService
{
    protected $repository;

    public function __construct(LocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }
}
