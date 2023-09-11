<?php

namespace App\Http\Controllers\Admin;

use App\Events\AddedProcessPlanEvent;
use App\Events\UpdateChartEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPlanRequest;
use App\Models\ProcessPlan;
use App\Repositories\OutgoingProductRepository;
use App\Repositories\ProcessPlanRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProcessPlanController extends Controller
{
    protected $processPlanRepository;
    protected $outgoingProductRepository;

    public function __construct(ProcessPlanRepository $processPlanRepository, OutgoingProductRepository $outgoingProductRepository)
    {
        $this->processPlanRepository = $processPlanRepository;
        $this->outgoingProductRepository = $outgoingProductRepository;
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
        $rpp = $this->processPlanRepository->create($input);
        $currentMonth = now()->month;
        $formattedCurrentMonth = now()->format('M');

        foreach ($input['selected_products'] as $productId => $productData) {
            $inputOutPro = [
                'process_plan_id' => $rpp->id,
                'product_id' => $productId,
                'qty' => $productData['qty'],
            ];

            $this->outgoingProductRepository->create($inputOutPro);
        }

        $rpp->whereMonth('created_at', $currentMonth)
            ->whereHas('outgoing_products.product.material', function ($query) {
                $query->where('id', 3);
            })
            ->get();

        $data = [];
        $labels = [];

        $totalSalesQty = $rpp->outgoing_products->sum('qty');

        $data[] = $totalSalesQty;
        $labels[] = $rpp->customer;

        $qty = $this->processPlanRepository->currentMonth($currentMonth);
        event(new UpdateChartEvent('tChart', $labels, $data));
        event(new UpdateChartEvent('rChart', $formattedCurrentMonth, $qty));
        return redirect()->route('rpp.index')->with('success', 'RPP berhasil dibuat !');
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

        foreach ($input['selected_products'] as $productId => $productData) {
            if (isset($rpp->outgoing_products[$productId])) {
                $outgoingProduct = $rpp->outgoing_products[$productId];
                $outgoingProduct->qty = $productData['qty'];
                $outgoingProduct->save();
            }
        }

        $rpp->update([
            'customer' => $input['customer'],
            'order_type' => $input['order_type'],
            'code' => $input['code'],
        ]);

        return redirect()->route('rpp.index')->with('success', 'RPP berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        $this->processPlanRepository->delete($id);
        return redirect()->back()->with('success', 'RPP berhasil dihapus');
    }
}
