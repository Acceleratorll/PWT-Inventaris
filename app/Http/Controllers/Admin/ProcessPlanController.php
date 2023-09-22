<?php

namespace App\Http\Controllers\Admin;

use App\Events\AddChartEvent;
use App\Events\DataAddedEvent;
use App\Events\DeleteChartEvent;
use App\Events\DeletedDataEvent;
use App\Events\UpdateChartEvent;
use App\Exports\ProcessPlansExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPlanRequest;
use App\Imports\ProcessPlansImport;
use App\Repositories\MaterialRepository;
use App\Repositories\OutgoingProductRepository;
use App\Repositories\ProcessPlanRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProcessPlanController extends Controller
{
    protected $processPlanRepository;
    protected $outgoingProductRepository;
    protected $productRepository;
    protected $materialRepository;

    public function __construct(ProcessPlanRepository $processPlanRepository, OutgoingProductRepository $outgoingProductRepository, ProductRepository $productRepository, MaterialRepository $materialRepository)
    {
        $this->processPlanRepository = $processPlanRepository;
        $this->outgoingProductRepository = $outgoingProductRepository;
        $this->productRepository = $productRepository;
        $this->materialRepository = $materialRepository;
    }

    public function index(): View
    {
        return view('rpp.index');
    }

    public function getRpps()
    {
        $rpps = $this->processPlanRepository->all();

        return DataTables::of($rpps)
            ->addColumn('customer', function ($rpp) {
                return $rpp->customer;
            })
            ->addColumn('code', function ($rpp) {
                return $rpp->code;
            })
            ->addColumn('order_type', function ($rpp) {
                return $rpp->order_type;
            })
            ->addColumn('formatted_created_at', function ($rpp) {
                return $rpp->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($rpp) {
                return $rpp->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.process-plan-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view('rpp.create');
    }

    public function store(ProcessPlanRequest $processPlanRequest)
    {
        $input = $processPlanRequest->validated();
        if ($input) {
            $rpp = $this->processPlanRepository->create($input);
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $formattedCurrentMonth = now()->format('M');

            $datasets = [];
            $amountChanges = [];

            foreach ($input['selected_products'] as $productId => $productData) {
                $inputOutPro = [
                    'process_plan_id' => $rpp->id,
                    'product_id' => $productId,
                    'qty' => $productData['qty'],
                ];

                $netChange = $productData['qty'];
                if (!isset($amountChanges[$productId])) {
                    $amountChanges[$productId] = 0;
                }
                $amountChanges[$productId] += $netChange;

                $this->outgoingProductRepository->create($inputOutPro);

                $product = $this->productRepository->find($productId);
                $this->productRepository->update($productId, ['amount' => $product->amount - $productData['qty']]);
            }

            $qty = $this->processPlanRepository->qtyCurrentMonth($currentMonth, $currentYear);

            $rppChart = [
                'id' => $rpp->id,
                'name' => $formattedCurrentMonth,
                'qty' => $qty,
                'context' => 'add'
            ];

            event(new UpdateChartEvent('rChart', $rppChart));


            $materials = $this->materialRepository->all();

            $data = [];
            $labels = [];

            foreach ($materials as $material) {
                $totalSalesQty = $rpp->outgoing_products
                    ->where('product.material.id', $material->id)
                    ->sum('qty');
                $data[] = $totalSalesQty;
                $labels[] = $material->name;
            }

            $datasets[] = [
                'labels' => $labels,
                'qty' => $data,
            ];

            $addedData = [
                'name' => $rpp->customer,
                'qty' => $data,
                'context' => 'create'
            ];

            $toastData = [
                'name' => $rpp->customer,
                'qty' => $data,
                'context' => 'create'
            ];

            event(new AddChartEvent('tChart', $addedData));
            event(new DataAddedEvent($toastData, 'Rpp'));
            return redirect()->route('rpp.index')->with('success', 'RPP berhasil dibuat !');
        }
        return redirect()->back()->with('error', 'Data isnt correct !');
    }

    public function show(string $id)
    {
        $rpp = $this->processPlanRepository->find($id);
        return response()->json($rpp);
    }

    public function edit(string $id): View
    {
        $rpp = $this->processPlanRepository->find($id);
        return view('rpp.edit', compact('rpp'));
    }

    public function update(ProcessPlanRequest $request, string $id)
    {
        $input = $request->validated();
        $rpp = $this->processPlanRepository->find($id);

        $amountChanges = [];

        foreach ($input['selected_products'] as $productId => $productData) {
            $outgoingProduct = $rpp->outgoing_products->firstWhere('product_id', $productId);

            if ($outgoingProduct) {
                $netChange = $productData['qty'] - $outgoingProduct['qty'];
                if (!isset($amountChanges[$productId])) {
                    $amountChanges[$productId] = 0;
                }
                $amountChanges[$productId] += $netChange;

                $outgoingProduct->qty = $productData['qty'];
                $outgoingProduct->save();
            } else {
                $inputOutPro = [
                    'process_plan_id' => $rpp->id,
                    'product_id' => $productId,
                    'qty' => $productData['qty'],
                ];

                $this->outgoingProductRepository->create($inputOutPro);

                $netChange = $productData['qty'];
                if (!isset($amountChanges[$productId])) {
                    $amountChanges[$productId] = 0;
                }
                $amountChanges[$productId] += $netChange;
            }
        }

        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->amount += $netChange;
            $product->save();
        }

        $materials = $this->materialRepository->all();

        $data = [];
        $labels = [];

        foreach ($materials as $material) {
            $totalSalesQty = $rpp->outgoing_products
                ->where('product.material.id', $material->id)
                ->sum('qty');
            $data[] = $totalSalesQty;
            $labels[] = $material->name;
        }

        $addedData = [
            'name' => $rpp->customer,
            'qty' => $data,
            'context' => 'update'
        ];

        event(new UpdateChartEvent('tChart', $addedData));
        return redirect()->route('rpp.index')->with('success', 'RPP berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        $rpp = $this->processPlanRepository->find($id);
        $qty = $this->processPlanRepository->qtyCurrentMonth(now()->month, now()->year);
        $data = [
            'name' => $rpp->customer,
            'qty' => $qty,
            'context' => 'delete',
        ];

        $datasets = [
            'name' => now()->format('M'),
            'qty' => (int)$qty - 1,
            'context' => 'delete',
        ];

        $this->processPlanRepository->delete($id);

        event(new DeleteChartEvent('tChart', $data));
        event(new UpdateChartEvent('rChart', $datasets));
        event(new DeletedDataEvent($data, 'Rpp'));

        return redirect()->back()->with('success', 'RPP berhasil dihapus');
    }

    public function exportProcessPlans()
    {
        $processPlans = $this->processPlanRepository->all();

        return (new ProcessPlansExport($processPlans))->download('process_plans.xlsx');
    }

    public function importProcessPlans()
    {
        try {
            DB::beginTransaction();

            Excel::import(new ProcessPlansImport, request()->file('file'));
            DB::commit();

            return redirect()->back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
