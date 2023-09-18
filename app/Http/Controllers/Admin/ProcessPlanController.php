<?php

namespace App\Http\Controllers\Admin;

use App\Events\AddChartEvent;
use App\Events\AddedProcessPlanEvent;
use App\Events\DataAddedEvent;
use App\Events\DeleteChartEvent;
use App\Events\DeletedDataEvent;
use App\Events\UpdateChartEvent;
use App\Events\UpdateDataEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPlanRequest;
use App\Models\ProcessPlan;
use App\Repositories\MaterialRepository;
use App\Repositories\OutgoingProductRepository;
use App\Repositories\ProcessPlanRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
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

            foreach ($input['selected_products'] as $productId => $productData) {
                $inputOutPro = [
                    'process_plan_id' => $rpp->id,
                    'product_id' => $productId,
                    'qty' => $productData['qty'],
                ];

                $this->outgoingProductRepository->create($inputOutPro);

                $product = $this->productRepository->find($productId);
                $this->productRepository->update($productId, ['amount' => $product->amount - $productData['qty']]);
            }

            $qty = $this->processPlanRepository->qtyCurrentMonth($currentMonth, $currentYear);

            $rppChart = [
                'id' => $rpp->id,
                'name' => $formattedCurrentMonth,
                'qty' => $qty,
                'context' => 'update'
            ];

            event(new UpdateChartEvent('rChart', $rppChart));


            $materials = $this->materialRepository->all();

            $data = [];
            $labels = [];

            foreach ($materials as $material) {
                $processPlans = $this->processPlanRepository->currentMonth($currentMonth, $currentYear);

                foreach ($processPlans as $processPlan) {
                }
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

        // Initialize an array to track the net change in product amount
        $amountChanges = [];

        // Loop through selected products to update or create outgoing_products
        foreach ($input['selected_products'] as $productId => $productData) {
            $outgoingProduct = $rpp->outgoing_products->firstWhere('product_id', $productId);

            if ($outgoingProduct) {
                // Calculate the net change and track it
                $netChange = $productData['qty'] - $outgoingProduct['qty'];
                var_dump($netChange);
                if (!isset($amountChanges[$productId])) {
                    $amountChanges[$productId] = 0;
                }
                $amountChanges[$productId] += $netChange;

                // Update existing outgoing_product
                $outgoingProduct->qty = $productData['qty'];
                $outgoingProduct->save();
            } else {
                // Create a new outgoing_product if it doesn't exist
                $inputOutPro = [
                    'process_plan_id' => $rpp->id,
                    'product_id' => $productId,
                    'qty' => $productData['qty'],
                ];

                $this->outgoingProductRepository->create($inputOutPro);

                // Calculate the net change and track it
                $netChange = $productData['qty'];
                if (!isset($amountChanges[$productId])) {
                    $amountChanges[$productId] = 0;
                }
                $amountChanges[$productId] += $netChange;
            }
        }

        // Now, apply the net changes to product amounts
        foreach ($amountChanges as $productId => $netChange) {
            $product = $this->productRepository->find($productId);
            $product->amount += $netChange; // Use += to add or subtract as needed
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
            'context' => 'update',
        ];

        $this->processPlanRepository->delete($id);

        event(new DeleteChartEvent('tChart', $data));
        event(new UpdateChartEvent('rChart', $datasets));
        event(new DeletedDataEvent($data, 'Rpp'));

        return redirect()->back()->with('success', 'RPP berhasil dihapus');
    }
}
