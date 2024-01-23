<?php

namespace App\Http\Controllers\Admin;

use App\Events\UpdateChartEvent;
use App\Exports\NotaDinasExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotaDinasRequest;
use App\Imports\NotaDinasImport;
use App\Services\NotaDinasService;
use App\Services\ProductPlanningService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class NotaDinasController extends Controller
{
    protected $notaDinasService;
    protected $productPlanningService;

    public function __construct(
        NotaDinasService $notaDinasService,
        ProductPlanningService $productPlanningService,
    ) {
        $this->notaDinasService = $notaDinasService;
        $this->productPlanningService = $productPlanningService;
    }

    public function index(): View
    {
        return view('notaDinas.index');
    }

    public function create(): View
    {
        return view('notaDinas.create');
    }

    public function store(NotaDinasRequest $notaDinasRequest)
    {
        $input = $notaDinasRequest->validated();
        try {
            DB::transaction(function () use ($input) {
                $notaDinas = $this->notaDinasService->create($input);
                foreach ($input['selected_products'] as $productId => $proPlanData) {
                    $proPlan = [
                        'product_id' => $productId,
                        'nota_dinas_id' => $notaDinas->id,
                        'requirement_amount' => $proPlanData['requirement_amount'],
                        'product_amount' => $proPlanData['product_amount'],
                        'procurement_plan_amount' => $proPlanData['procurement_plan_amount'],
                    ];
                    $this->productPlanningService->create($proPlan);
                }
            });
            event(new UpdateChartEvent('pPChart', $input));
            return redirect()->route('notaDinas.index')->with('success', 'Nota dinas created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th);
        }
    }

    public function show($id): JsonResponse
    {
        $data = $this->notaDinasService->getById($id);
        return response()->json(['data' => $data, 'message' => 'Data has been found!'], 200);
    }

    public function edit($id): View
    {
        $data = $this->notaDinasService->getById($id);
        return view('notaDinas.edit', compact('data'));
    }

    public function update($id, NotaDinasRequest $notaDinasRequest)
    {
        try {
            $input = $notaDinasRequest->validated();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function approve($id)
    {
        $this->notaDinasService->getById($id)->update(['authorized' => 1]);
        return redirect()->route('notaDinas.index')->with('success', 'Data status changed to Approved!');
    }

    public function wait($id)
    {
        $this->notaDinasService->getById($id)->update(['authorized' => 0]);
        return redirect()->route('notaDinas.index')->with('success', 'Data status changed to Wait!');
    }

    public function decline($id)
    {
        $this->notaDinasService->getById($id)->update(['authorized' => 2]);
        return redirect()->route('notaDinas.index')->with('success', 'Data status changed to Declined!');
    }

    public function destroy($id): JsonResponse
    {
        $this->notaDinasService->delete($id);
        return response()->json(['message' => 'Data deleted successfully!'], 200);
    }

    public function table()
    {
        return $this->notaDinasService->table();
    }

    public function tableAuthorized()
    {
        $data = $this->notaDinasService->getByAuthorized(1);
        return $this->notaDinasService->tableParam($data);
    }

    public function select(Request $request)
    {
        $term = $request->term;
        return $this->notaDinasService->select($term);
    }

    public function import()
    {
        try {
            DB::Transaction(function () {
                Excel::import(new NotaDinasImport, request()->file('file'));
            });
            return redirect()->back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $data = $this->notaDinasService->all();

        return (new NotaDinasExport($data))->download('nota_dinas.xlsx');
    }
}
