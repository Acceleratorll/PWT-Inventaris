<?php

namespace App\Http\Controllers\Admin;

use App\Events\AddChartEvent;
use App\Events\DataAddedEvent;
use App\Events\DeletedDataEvent;
use App\Events\ProductNotificationEvent;
use App\Events\UpdateChartEvent;
use App\Exports\ProcessPlansExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPlanRequest;
use App\Imports\ProcessPlansImport;
use App\Notifications\CriticalProduct;
use App\Notifications\WarningProduct;
use App\Repositories\MaterialRepository;
use App\Repositories\OutgoingProductRepository;
use App\Repositories\ProcessPlanRepository;
use App\Repositories\ProductLocationRepository;
use App\Repositories\ProductRepository;
use App\Services\ProcessPlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProcessPlanController extends Controller
{
    protected $processPlanRepository;
    protected $outgoingProductRepository;
    protected $productRepository;
    protected $productLocationRepository;
    protected $materialRepository;
    protected $processPlanService;

    public function __construct(
        ProcessPlanRepository $processPlanRepository,
        OutgoingProductRepository $outgoingProductRepository,
        ProductRepository $productRepository,
        ProductLocationRepository $productLocationRepository,
        MaterialRepository $materialRepository,
        ProcessPlanService $processPlanService,
    ) {
        $this->processPlanRepository = $processPlanRepository;
        $this->outgoingProductRepository = $outgoingProductRepository;
        $this->productRepository = $productRepository;
        $this->productLocationRepository = $productLocationRepository;
        $this->materialRepository = $materialRepository;
        $this->processPlanService = $processPlanService;
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
                return $rpp->customer->name;
            })
            ->addColumn('code', function ($rpp) {
                return $rpp->code;
            })
            ->addColumn('order_type', function ($rpp) {
                return $rpp->order_type->name;
            })
            ->addColumn('products', function ($rpp) {
                $productList = '<ul>';
                foreach ($rpp->outgoing_products as $product) {
                    $productList .= '<li>' . $product->product->name . ' | (Qty: ' . $product->amount . ')</li>';
                }
                $productList .= '</ul>';
                return $productList;
            })
            ->addColumn('desc', function ($rpp) {
                return $rpp->desc;
            })
            ->addColumn('formatted_created_at', function ($rpp) {
                return $rpp->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($rpp) {
                return $rpp->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.process-plan-action')
            ->rawColumns(['action', 'products'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view('rpp.create');
    }

    public function store(ProcessPlanRequest $processPlanRequest)
    {
        try {
            $input = $processPlanRequest->validated();
            if ($input) {
                DB::transaction(function () use ($input) {
                    $user = auth()->user();
                    $rpp = $this->processPlanRepository->create($input);
                    $formattedCurrentMonth = now()->format('M');
                    $datasets = [];
                    $amountChanges = [];
                    foreach ($input['selected_products'] as $productId => $productData) {
                        foreach ($productData['pro_loc_ids'] as $proLocId => $proLocData) {
                            $product = $this->productRepository->find($productId);
                            $inputOutPro = [
                                'process_plan_id' => $rpp->id,
                                'product_id' => $productId,
                                'amount' => $proLocData['amount'],
                                'product_amount' => $product->total_amount,
                                'expired' => $proLocData['expired'],
                            ];

                            $netChange = $proLocData['amount'];

                            if (!isset($amountChanges[$productId])) {
                                $amountChanges[$productId] = 0;
                            }

                            $amountChanges[$productId] += $netChange;

                            $this->outgoingProductRepository->create($inputOutPro);
                            $proLoc = $this->productLocationRepository->find($proLocId);
                            $proLoc->update(['amount' => $proLoc->amount -= $proLocData['amount']]);
                        }
                    }

                    foreach ($rpp->outgoing_products as $oProduct) {
                        $cproduct = $this->productRepository->find($oProduct->product_id);
                        if ($cproduct->amount <= (0.1 * $cproduct->max_amount)) {
                            auth()->user()->notify(new CriticalProduct($cproduct));
                            $notif = $user->unreadNotifications->where('data.type', 'critical')->last();
                            event(new ProductNotificationEvent('critical', $cproduct, $notif->data['message']));
                        } else if ($cproduct->amount <= (0.3 * $cproduct->max_amount)) {
                            auth()->user()->notify(new WarningProduct($cproduct));
                            $notif = $user->unreadNotifications->where('data.type', 'warning')->last();
                            event(new ProductNotificationEvent('warning', $cproduct, $notif->data['message']));
                        }
                    }

                    // $rppChart = [
                    //     'id' => $rpp->id,
                    //     'name' => $formattedCurrentMonth,
                    //     'context' => 'add'
                    // ];
                    // event(new UpdateChartEvent('rChart', $rppChart));
                    // $materials = $this->materialRepository->all();
                    // $data = [];
                    // $labels = [];
                    // foreach ($materials as $material) {
                    //     $totalSalesQty = $rpp->outgoing_products
                    //         ->where('product.material.id', $material->id)
                    //         ->sum('qty');
                    //     $data[] = $totalSalesQty;
                    //     $labels[] = $material->name;
                    // }
                    // $datasets[] = [
                    //     'labels' => $labels,
                    //     'qty' => $data,
                    // ];
                    // $addedData = [
                    //     'name' => $rpp->customer->name,
                    //     'qty' => $data,
                    //     'context' => 'create'
                    // ];
                    // $toastData = [
                    //     'name' => $rpp->customer->name,
                    //     'qty' => $data,
                    //     'context' => 'create'
                    // ];

                    // event(new AddChartEvent('tChart', $addedData));
                    // event(new DataAddedEvent($toastData, 'Rpp'));

                    DB::commit();
                });

                dd('success!', $processPlanRequest);
                return redirect()->route('rpp.index')->with('success', 'RPP berhasil dibuat !');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        $user = auth()->user();
        $rpp = $this->processPlanRepository->find($id);
        $amountChanges = [];
        foreach ($input['selected_products'] as $productId => $productData) {
            $outgoingProduct = $rpp->outgoing_products->firstWhere('product_id', $productId);
            if ($outgoingProduct) {
                $netChange = $outgoingProduct['qty'] - $productData['qty'];
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

        foreach ($rpp->outgoing_products as $oProduct) {
            $cproduct = $this->productRepository->find($oProduct->product_id);
            if ($cproduct->amount <= (0.1 * $cproduct->max_amount)) {
                auth()->user()->notify(new CriticalProduct($cproduct));
                $notif = $user->unreadNotifications->where('data.type', 'critical')->last();
                event(new ProductNotificationEvent('critical', $cproduct, $notif->data['message']));
            } else if ($cproduct->amount <= (0.3 * $cproduct->max_amount)) {
                auth()->user()->notify(new WarningProduct($cproduct));
                $notif = $user->unreadNotifications->where('data.type', 'warning')->last();
                event(new ProductNotificationEvent('warning', $cproduct, $notif->data['message']));
            }
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
        event(new UpdateChartEvent('rChart', $addedData));
        return redirect()->route('rpp.index')->with('success', 'RPP berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        $rpp = $this->processPlanRepository->find($id);
        $qty = $this->processPlanRepository->qtyCurrentMonth(now()->month, now()->year);
        $data = [
            'name' => $rpp->customer->name,
            'qty' => $qty,
            'context' => 'delete',
        ];

        $datasets = [
            'name' => now()->format('M'),
            'qty' => (int)$qty - 1,
            'context' => 'delete',
        ];

        $this->processPlanRepository->delete($id);

        event(new UpdateChartEvent('tChart', $datasets));
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

    public function getRppsByCustomerName($customer): JsonResponse
    {
        $rpps = $this->processPlanRepository->getByCustomerName($customer);
        return response()->json($rpps);
    }
}
