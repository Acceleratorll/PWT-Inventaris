<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\MaterialRepository;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $materialRepository;

    public function __construct(MaterialRepository $materialRepository)
    {
        $this->materialRepository = $materialRepository;
    }

    public function index(): View
    {
        return view('dashboard.index');
    }

    public function create(): View
    {
        return view('page');
    }

    public function search(Request $request)
    {
        $results = $this->materialRepository->search($request->input('term'));
        return response()->json(['results' => $results]);
    }
}
