<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\QualifierRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QualifierController extends Controller
{
    protected $qualifierRepository;

    public function __construct(QualifierRepository $qualifierRepository)
    {
        $this->qualifierRepository = $qualifierRepository;
    }

    public function getJsonQualifiers(Request $request): JsonResponse
    {
        $term = $request->term;
        $qualifiers = $this->qualifierRepository->search($term);
        $formattedQualifiers = $qualifiers->map(function ($qualifier) {
            return [
                'id' => $qualifier->id,
                'text' => $qualifier->name,
            ];
        });
        return response()->json($formattedQualifiers);
    }
}
