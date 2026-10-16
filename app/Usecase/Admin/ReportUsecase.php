<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportUsecase extends Usecase
{
    public function getReport(array $filterData = []): array
    {
        try {
            $startDate = $filterData['start_date'] ?? null;
            $endDate = $filterData['end_date'] ?? null;
            $type = $filterData['type'] ?? 'all';

            $query = DB::table(DatabaseConst::TRANSACTION().' as t')
                ->join(DatabaseConst::ITEM().' as i', 't.item_id', '=', 'i.id')
                ->leftJoin(DatabaseConst::USER().' as u', 't.user_id', '=', 'u.id')
                ->select(
                    't.*',
                    'i.name as item_name',
                    'i.code as item_code',
                    'i.unit as item_unit',
                    'u.name as user_name'
                )
                ->when($startDate, function ($q, $sd) {
                    $q->where('t.date', '>=', $sd);
                })
                ->when($endDate, function ($q, $ed) {
                    $q->where('t.date', '<=', $ed);
                })
                ->when($type && $type !== 'all', function ($q) use ($type) {
                    $q->where('t.type', $type);
                })
                ->orderBy('t.date', 'desc')
                ->orderBy('t.id', 'desc');

            $transactions = $query->get();

            // Ringkasan transaksi periode ini
            $totalInQty = $transactions->where('type', 'in')->sum('quantity');
            $totalOutQty = $transactions->where('type', 'out')->sum('quantity');

            // Ringkasan stok saat ini
            $currentStock = DB::table(DatabaseConst::ITEM())
                ->whereNull('deleted_at')
                ->select('code', 'name', 'category', 'unit', 'stock')
                ->orderBy('name', 'asc')
                ->get();

            $totalStock = $currentStock->sum('stock');

            return Response::buildSuccess([
                'transactions' => $transactions,
                'current_stock' => $currentStock,
                'summary' => [
                    'total_transactions' => $transactions->count(),
                    'total_in_quantity' => $totalInQty,
                    'total_out_quantity' => $totalOutQty,
                    'total_current_stock' => $totalStock,
                ],
                'filter' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'type' => $type,
                ],
            ], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
