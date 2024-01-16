<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $repository;
    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function edit($id): View
    {
        $data = $this->repository->find($id);
        return view('user.edit', compact('data'));
    }
}
