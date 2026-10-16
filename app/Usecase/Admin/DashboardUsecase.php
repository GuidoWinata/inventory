<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardUsecase extends Usecase
{
    public function getDashboardData(): array
    {
        try {
            $totalItemTypes = DB::table(DatabaseConst::ITEM())
                ->whereNull('deleted_at')
                ->count();

            $totalPhysicalStock = DB::table(DatabaseConst::ITEM())
                ->whereNull('deleted_at')
                ->sum('stock');

            $totalTransactionsIn = DB::table(DatabaseConst::TRANSACTION())
                ->where('type', 'in')
                ->count();

            $totalTransactionsOut = DB::table(DatabaseConst::TRANSACTION())
                ->where('type', 'out')
                ->count();

            $latestTransactions = DB::table(DatabaseConst::TRANSACTION().' as t')
                ->join(DatabaseConst::ITEM().' as i', 't.item_id', '=', 'i.id')
                ->leftJoin(DatabaseConst::USER().' as u', 't.user_id', '=', 'u.id')
                ->select(
                    't.id',
                    't.date',
                    't.type',
                    't.quantity',
                    't.notes',
                    't.created_at',
                    'i.name as item_name',
                    'i.code as item_code',
                    'i.unit as item_unit',
                    'u.name as user_name'
                )
                ->orderBy('t.date', 'desc')
                ->orderBy('t.id', 'desc')
                ->limit(5)
                ->get();

            return Response::buildSuccess([
                'total_item_types' => $totalItemTypes,
                'total_physical_stock' => (int) $totalPhysicalStock,
                'total_transactions_in' => $totalTransactionsIn,
                'total_transactions_out' => $totalTransactionsOut,
                'latest_transactions' => $latestTransactions,
            ], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
