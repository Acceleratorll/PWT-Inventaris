<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\OutgoingProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OutgoingProductController extends Controller
{
    protected $outgoingProductRepository;

    public function __construct(OutgoingProductRepository $outgoingProductRepository)
    {
        $this->outgoingProductRepository = $outgoingProductRepository;
    }

    public function getJsonOutProductsByProduct($product): JsonResponse
    {
        $outgoing_product = $this->outgoingProductRepository;
        return response()->json();
    }

    public function getJsonOutProduct($outgoingProduct): JsonResponse
    {
        return response()->json($this->outgoingProductRepository->find($outgoingProduct));
    }
}
