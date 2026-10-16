<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Usecase\Admin\ReportUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    protected array $page = [
        'route' => 'reports',
        'title' => 'Laporan Stok',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected ReportUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    public function index(Request $request): View|Response
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'all');

        $reportResult = $this->usecase->getReport([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
        ]);

        $reportData = $reportResult['data'] ?? [
            'transactions' => collect(),
            'current_stock' => collect(),
            'summary' => [
                'total_transactions' => 0,
                'total_in_quantity' => 0,
                'total_out_quantity' => 0,
                'total_current_stock' => 0,
            ],
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => $type,
            ],
        ];

        return view('_admin.reports.index', [
            'report' => $reportData,
            'page' => $this->page,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => $type,
        ]);
    }
}
