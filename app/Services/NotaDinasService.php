<?php

namespace App\Services;

use App\Repositories\NotaDinasRepository;

class NotaDinasService
{
    protected $repository;

    public function __construct(NotaDinasRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }
}
