<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionUsecase extends Usecase
{
    public function getAll(array $filterData = []): array
    {
        try {
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
                ->when($filterData['type'] ?? false, function ($q, $type) {
                    if ($type !== 'all') {
                        $q->where('t.type', $type);
                    }
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('i.name', 'like', '%'.$keywords.'%')
                            ->orWhere('i.code', 'like', '%'.$keywords.'%')
                            ->orWhere('t.notes', 'like', '%'.$keywords.'%');
                    });
                })
                ->orderBy('t.date', 'desc')
                ->orderBy('t.id', 'desc');

            if (! empty($filterData['no_pagination'])) {
                $data = $query->get();
            } else {
                $data = $query->paginate(15);
                if (! empty($filterData)) {
                    $data->appends($filterData);
                }
            }

            return Response::buildSuccess(['list' => $data], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getItemsForDropdown(): array
    {
        try {
            $items = DB::table(DatabaseConst::ITEM())
                ->whereNull('deleted_at')
                ->select('id', 'code', 'name', 'unit', 'stock')
                ->orderBy('name', 'asc')
                ->get();

            return Response::buildSuccess($items);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getByID(int $id): array
    {
        try {
            $data = DB::table(DatabaseConst::TRANSACTION().' as t')
                ->join(DatabaseConst::ITEM().' as i', 't.item_id', '=', 'i.id')
                ->leftJoin(DatabaseConst::USER().' as u', 't.user_id', '=', 'u.id')
                ->select(
                    't.*',
                    'i.name as item_name',
                    'i.code as item_code',
                    'i.unit as item_unit',
                    'u.name as user_name'
                )
                ->where('t.id', $id)
                ->first();

            if (! $data) {
                return Response::buildErrorNotFound();
            }

            return Response::buildSuccess(data: collect($data)->toArray());
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function create(Request $data): array
    {
        $validator = Validator::make($data->all(), [
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validator->validate();

        DB::beginTransaction();
        try {
            $item = DB::table(DatabaseConst::ITEM())
                ->where('id', $data['item_id'])
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (! $item) {
                DB::rollback();

                return Response::buildErrorNotFound('Barang tidak ditemukan atau telah dihapus.');
            }

            $qty = (int) $data['quantity'];

            if ($data['type'] === 'out') {
                if ($qty > $item->stock) {
                    DB::rollback();

                    return Response::buildError(
                        code: ResponseConst::HTTP_BAD_REQUEST,
                        message: "Stok tidak mencukupi! Sisa stok saat ini: {$item->stock}"
                    );
                }
                $newStock = $item->stock - $qty;
            } else {
                $newStock = $item->stock + $qty;
            }

            // Insert transaction
            DB::table(DatabaseConst::TRANSACTION())->insert([
                'item_id' => $item->id,
                'user_id' => Auth::user()?->id,
                'type' => $data['type'],
                'quantity' => $qty,
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update item stock
            DB::table(DatabaseConst::ITEM())
                ->where('id', $item->id)
                ->update([
                    'stock' => $newStock,
                    'updated_by' => Auth::user()?->id,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return Response::buildSuccessCreated(
                message: 'Transaksi berhasil dicatat dan stok telah diperbarui.'
            );
        } catch (Exception $e) {
            DB::rollback();
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
