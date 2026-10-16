<?php

namespace Database\Seeders;

use App\Constants\DatabaseConst;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemAndTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@inventory.test')->first() ?? User::first();
        $staff = User::where('email', 'staff@inventory.test')->first() ?? $admin;

        $now = now();

        $items = [
            [
                'code' => 'BRG-001',
                'name' => 'Laptop ASUS VivoBook',
                'category' => 'Elektronik',
                'unit' => 'Unit',
                'stock' => 15,
                'created_by' => $admin?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'BRG-002',
                'name' => 'Mouse Wireless Logitech',
                'category' => 'Elektronik',
                'unit' => 'Pcs',
                'stock' => 45,
                'created_by' => $admin?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'BRG-003',
                'name' => 'Kertas HVS A4 80gr',
                'category' => 'ATK',
                'unit' => 'Rim',
                'stock' => 8,
                'created_by' => $admin?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'BRG-004',
                'name' => 'Pulpen Gel Hitam Box',
                'category' => 'ATK',
                'unit' => 'Box',
                'stock' => 0,
                'created_by' => $admin?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'BRG-005',
                'name' => 'Kabel HDMI 2 Meter',
                'category' => 'Elektronik',
                'unit' => 'Pcs',
                'stock' => 25,
                'created_by' => $admin?->id,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($items as $itemData) {
            $existing = DB::table(DatabaseConst::ITEM())
                ->where('code', $itemData['code'])
                ->first();

            if (! $existing) {
                $itemId = DB::table(DatabaseConst::ITEM())->insertGetId($itemData);

                // Initial transactions
                if ($itemData['code'] === 'BRG-001') {
                    DB::table(DatabaseConst::TRANSACTION())->insert([
                        [
                            'item_id' => $itemId,
                            'user_id' => $staff?->id,
                            'type' => 'in',
                            'quantity' => 20,
                            'date' => now()->subDays(3)->toDateString(),
                            'notes' => 'Pengadaan barang awal dari Supplier A',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'item_id' => $itemId,
                            'user_id' => $staff?->id,
                            'type' => 'out',
                            'quantity' => 5,
                            'date' => now()->subDay()->toDateString(),
                            'notes' => 'Distribusi untuk divisi IT',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ]);
                } elseif ($itemData['code'] === 'BRG-003') {
                    DB::table(DatabaseConst::TRANSACTION())->insert([
                        [
                            'item_id' => $itemId,
                            'user_id' => $staff?->id,
                            'type' => 'in',
                            'quantity' => 10,
                            'date' => now()->subDays(5)->toDateString(),
                            'notes' => 'Pembelian ATK bulanan',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'item_id' => $itemId,
                            'user_id' => $staff?->id,
                            'type' => 'out',
                            'quantity' => 2,
                            'date' => now()->subDays(2)->toDateString(),
                            'notes' => 'Permintaan bagian administrasi',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ]);
                } elseif ($itemData['code'] === 'BRG-004') {
                    DB::table(DatabaseConst::TRANSACTION())->insert([
                        [
                            'item_id' => $itemId,
                            'user_id' => $staff?->id,
                            'type' => 'in',
                            'quantity' => 10,
                            'date' => now()->subDays(10)->toDateString(),
                            'notes' => 'Stok awal',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'item_id' => $itemId,
                            'user_id' => $staff?->id,
                            'type' => 'out',
                            'quantity' => 10,
                            'date' => now()->subDay()->toDateString(),
                            'notes' => 'Pengambilan untuk ujian semester',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ]);
                }
            }
        }
    }
}
