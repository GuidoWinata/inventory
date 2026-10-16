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

class ItemUsecase extends Usecase
{
    public function getAll(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::ITEM().' as i')
                ->select('i.*')
                ->whereNull('i.deleted_at')
                ->when($filterData['keywords'] ?? false, function ($query, $keywords) {
                    return $query->where(function ($q) use ($keywords) {
                        $q->where('i.name', 'like', '%'.$keywords.'%')
                            ->orWhere('i.code', 'like', '%'.$keywords.'%')
                            ->orWhere('i.category', 'like', '%'.$keywords.'%');
                    });
                })
                ->orderBy('i.created_at', 'desc');

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

    public function getByID(int $id): array
    {
        try {
            $data = DB::table(DatabaseConst::ITEM())
                ->whereNull('deleted_at')
                ->where('id', $id)
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
            'code' => 'required|unique:items,code',
            'name' => 'required|min:2',
            'category' => 'required',
            'unit' => 'required',
            'stock' => 'nullable|integer|min:0',
        ]);

        $validator->validate();

        DB::beginTransaction();
        try {
            DB::table(DatabaseConst::ITEM())->insert([
                'code' => trim($data['code']),
                'name' => trim($data['name']),
                'category' => trim($data['category']),
                'unit' => trim($data['unit']),
                'stock' => (int) ($data['stock'] ?? 0),
                'created_by' => Auth::user()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return Response::buildSuccessCreated();
        } catch (Exception $e) {
            DB::rollback();
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function update(Request $data, int $id): array
    {
        $validator = Validator::make($data->all(), [
            'code' => 'required|unique:items,code,'.$id,
            'name' => 'required|min:2',
            'category' => 'required',
            'unit' => 'required',
        ]);

        $validator->validate();

        DB::beginTransaction();
        try {
            $payload = [
                'code' => trim($data['code']),
                'name' => trim($data['name']),
                'category' => trim($data['category']),
                'unit' => trim($data['unit']),
                'updated_by' => Auth::user()?->id,
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::ITEM())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update($payload);

            DB::commit();

            return Response::buildSuccess(message: ResponseConst::SUCCESS_MESSAGE_UPDATED);
        } catch (Exception $e) {
            DB::rollback();
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function delete(int $id): array
    {
        DB::beginTransaction();
        try {
            $delete = DB::table(DatabaseConst::ITEM())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_by' => Auth::user()?->id,
                    'deleted_at' => now(),
                ]);

            if (! $delete) {
                DB::rollback();
                throw new Exception('Gagal menghapus data barang.');
            }

            DB::commit();

            return Response::buildSuccess(message: ResponseConst::SUCCESS_MESSAGE_DELETED);
        } catch (Exception $e) {
            DB::rollback();
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
