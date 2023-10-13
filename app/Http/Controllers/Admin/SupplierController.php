<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Repositories\SupplierRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    protected $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function index(): View
    {
        return view('supplier.index');
    }

    public function create(): View
    {
        return view('supplier.create');
    }

    public function store(SupplierRequest $supplierRequest)
    {
        $input = $supplierRequest->validated();
        $supplier = $this->supplierRepository->create($input);
        return back();
    }

    public function show($supplier): JsonResponse
    {
        $supplier = $this->supplierRepository->find($supplier);
        return response()->json($supplier);
    }

    public function edit($supplier): View
    {
        $supplier = $this->supplierRepository->find($supplier);
        return view('supplier.edit');
    }

    public function update(SupplierRequest $supplierRequest, $supplier)
    {
        $input = $supplierRequest->validated();
        $this->supplierRepository->update($supplier, $input);
        return back();
    }

    public function destroy($supplier)
    {
        $this->supplierRepository->delete($supplier);
        return redirect()->back()->with('Supplier Deleted Successfully');
    }

    public function getSuppliers()
    {
        $suppliers = $this->supplierRepository->orderBy('created_at', 'desc');
        return DataTables::of($suppliers)
            ->addColumn('id', function ($supplier) {
                return $supplier->id;
            })
            ->addColumn('name', function ($supplier) {
                return $supplier->name;
            })
            ->addColumn('formatted_created_at', function ($supplier) {
                return $supplier->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($supplier) {
                return $supplier->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.supplier-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }
}
