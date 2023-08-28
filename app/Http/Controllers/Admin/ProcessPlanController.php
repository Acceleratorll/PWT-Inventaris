<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcessPlan;
use App\Repositories\ProcessPlanRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProcessPlanController extends Controller
{
    protected $processPlanRepository;

    public function __construct(ProcessPlanRepository $processPlanRepository)
    {
        $this->processPlanRepository = $processPlanRepository;
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
                return $rpp->created_at->format('d-m-Y');
            })
            ->addColumn('formatted_updated_at', function ($rpp) {
                return $rpp->updated_at->format('d-m-Y');
            })
            ->addColumn('action', 'partials.button-table.process-plan-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
