<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Usecase\Admin\DashboardUsecase;
use App\Usecase\Admin\SidebarMenuUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardUsecase $dashboardUsecase,
        protected SidebarMenuUsecase $sidebarMenuUsecase
    ) {}

    public function index(): View|Response
    {
        $dashboardData = $this->dashboardUsecase->getDashboardData();
        $stats = $dashboardData['data'] ?? [
            'total_item_types' => 0,
            'total_physical_stock' => 0,
            'total_transactions_in' => 0,
            'total_transactions_out' => 0,
            'latest_transactions' => collect(),
        ];

        $modules = $this->sidebarMenuUsecase->getDashboardModules(
            accessType: (int) Auth::user()->access_type
        );

        $allowedRoutes = [
            'admin.items.index',
            'admin.transactions.index',
            'admin.reports.index',
            'admin.users.index',
            'admin.sidebar_menu.index',
        ];

        $modules = collect($modules['data'] ?? [])
            ->filter(fn ($menu) => in_array($menu->route_name, $allowedRoutes, true))
            ->values()
            ->all();

        return view('_admin.dashboard', [
            'stats' => $stats,
            'modules' => $modules,
        ]);
    }
}
