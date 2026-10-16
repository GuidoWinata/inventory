<?php

namespace Database\Seeders;

use App\Constants\DatabaseConst;
use App\Constants\UserConst;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SidebarMenuSeeder extends Seeder
{
    /**
     * Seed sidebar_menu_groups, sidebar_menus, and sidebar_menu_accesses tables.
     */
    public function run(): void
    {
        DB::table(DatabaseConst::SIDEBAR_MENU_GROUP())->truncate();
        DB::table(DatabaseConst::SIDEBAR_MENU())->truncate();
        DB::table(DatabaseConst::SIDEBAR_MENU_ACCESS())->truncate();

        $now = now();

        // =====================================================================
        // GROUPS
        // =====================================================================
        DB::table(DatabaseConst::SIDEBAR_MENU_GROUP())->insert([
            ['key' => 'utama',     'label' => 'Utama',     'color' => 'blue',    'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $admin = UserConst::ADMIN;
        $staff = UserConst::STAFF;
        $manager = UserConst::MANAGER;

        $allRoles = [$admin, $staff, $manager];
        $adminOnly = [$admin];
        $staffOnly = [$staff];
        $managerOnly = [$manager];

        // =====================================================================
        // GROUP: utama
        // =====================================================================
        // 1. Dashboard (All roles)
        $dashboardId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Dashboard',
            'route_name' => 'admin.dashboard',
            'icon' => '_admin._layout.icons.sidebar.dashboard',
            'group' => 'utama',
            'sort_order' => 10,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($dashboardId, $allRoles, $now);

        // 2. Data Stok Barang (All roles)
        $itemsId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Stok Barang',
            'route_name' => 'admin.items.index',
            'icon' => '_admin._layout.icons.sidebar.item',
            'group' => 'utama',
            'sort_order' => 20,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($itemsId, $allRoles, $now);

        // 3. Barang Masuk & Keluar (Staff Gudang only)
        $transactionsId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Barang Masuk & Keluar',
            'route_name' => 'admin.transactions.index',
            'icon' => '_admin._layout.icons.sidebar.transaction',
            'group' => 'utama',
            'sort_order' => 30,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($transactionsId, $staffOnly, $now);

        // 4. Laporan Stok (Manager only)
        $reportsId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Laporan Stok',
            'route_name' => 'admin.reports.index',
            'icon' => '_admin._layout.icons.sidebar.report',
            'group' => 'utama',
            'sort_order' => 40,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($reportsId, $managerOnly, $now);

        // 5. Kelola Akun User (Admin only)
        $penggunaId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Pengguna Aplikasi',
            'route_name' => 'admin.users.index',
            'icon' => '_admin._layout.icons.sidebar.user',
            'group' => 'utama',
            'sort_order' => 50,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($penggunaId, $adminOnly, $now);

        // 6. Manajemen Sidebar (Admin only)
        $sidebarMgmtId = DB::table(DatabaseConst::SIDEBAR_MENU())->insertGetId([
            'label' => 'Manajemen Sidebar',
            'route_name' => 'admin.sidebar_menu.index',
            'icon' => '_admin._layout.icons.sidebar.data_master',
            'group' => 'utama',
            'sort_order' => 60,
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->assignAccess($sidebarMgmtId, $adminOnly, $now);

    }

    /**
     * Insert access records for a given sidebar menu item.
     *
     * @param  array<int>  $accessTypes
     */
    private function assignAccess(int $sidebarMenuId, array $accessTypes, mixed $now): void
    {
        $inserts = array_map(fn ($type) => [
            'sidebar_menu_id' => $sidebarMenuId,
            'access_type' => $type,
            'created_at' => $now,
        ], $accessTypes);

        DB::table(DatabaseConst::SIDEBAR_MENU_ACCESS())->insert($inserts);
    }
}
