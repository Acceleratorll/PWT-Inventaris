<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Repositories\CustomerRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(): View
    {
        return view('customer.index');
    }

    public function create(): View
    {
        return view('customer.create');
    }

    public function store(CustomerRequest $customerRequest)
    {
        $input = $customerRequest->validated();
        $this->customerRepository->create($input);
        return back();
    }

    public function show($customer): JsonResponse
    {
        $customer = $this->customerRepository->find($customer);
        return response()->json($customer);
    }

    public function edit($customer): View
    {
        $customer = $this->customerRepository->find($customer);
        return view('customer.edit');
    }

    public function update(CustomerRequest $customerRequest, $id)
    {
        $input = $customerRequest->validated();
        $this->customerRepository->update($id, $input);
        return redirect()->back()->with('Customer Updated Successfully');
    }

    public function destroy($customer)
    {
        $this->customerRepository->delete($customer);
        return redirect()->back()->with('Customer Deleted Successfully');
    }

    public function getCustomers()
    {
        $customers = $this->customerRepository->orderBy('created_at', 'desc');
        return DataTables::of($customers)
            ->addColumn('id', function ($customer) {
                return $customer->id;
            })
            ->addColumn('name', function ($customer) {
                return $customer->name;
            })
            ->addColumn('formatted_created_at', function ($customer) {
                return $customer->created_at->format('D, d-m-y, G:i');
            })
            ->addColumn('formatted_updated_at', function ($customer) {
                return $customer->updated_at->format('D, d-m-y, G:i');
            })
            ->addColumn('action', 'partials.button-table.customer-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function getJsonCustomers(Request $request): JsonResponse
    {
        $customers = $this->customerRepository->search($request->term);
        $formattedCustomers = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->name
            ];
        });
        return response()->json($formattedCustomers);
    }
}
