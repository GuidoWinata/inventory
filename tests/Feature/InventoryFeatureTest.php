<?php

use App\Constants\DatabaseConst;
use App\Constants\UserConst;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['email' => 'admin_test@inventory.test'],
        [
            'name' => 'Admin Test',
            'password' => bcrypt('password'),
            'access_type' => UserConst::ADMIN,
            'is_active' => 1,
        ]
    );

    $this->staff = User::firstOrCreate(
        ['email' => 'staff_test@inventory.test'],
        [
            'name' => 'Staff Test',
            'password' => bcrypt('password'),
            'access_type' => UserConst::STAFF,
            'is_active' => 1,
        ]
    );

    $this->manager = User::firstOrCreate(
        ['email' => 'manager_test@inventory.test'],
        [
            'name' => 'Manager Test',
            'password' => bcrypt('password'),
            'access_type' => UserConst::MANAGER,
            'is_active' => 1,
        ]
    );

    $this->withoutMiddleware([
        PreventRequestForgery::class,
    ]);
});

/*
|--------------------------------------------------------------------------
| RBAC / ACCESS PERMISSIONS TESTS
|--------------------------------------------------------------------------
*/

test('dashboard can be accessed by all roles', function () {
    $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertSuccessful();
    $this->actingAs($this->staff)->get(route('admin.dashboard'))->assertSuccessful();
    $this->actingAs($this->manager)->get(route('admin.dashboard'))->assertSuccessful();
});

test('admin can manage users but staff and manager are forbidden', function () {
    $this->actingAs($this->admin)->get(route('admin.users.index'))->assertSuccessful();
    $this->actingAs($this->staff)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($this->manager)->get(route('admin.users.index'))->assertForbidden();
});

test('all roles can view items, but only admin can access create/edit pages', function () {
    // All roles can view list
    $this->actingAs($this->admin)->get(route('admin.items.index'))->assertSuccessful();
    $this->actingAs($this->staff)->get(route('admin.items.index'))->assertSuccessful();
    $this->actingAs($this->manager)->get(route('admin.items.index'))->assertSuccessful();

    // Only admin can access add
    $this->actingAs($this->admin)->get(route('admin.items.add'))->assertSuccessful();
    $this->actingAs($this->staff)->get(route('admin.items.add'))->assertForbidden();
    $this->actingAs($this->manager)->get(route('admin.items.add'))->assertForbidden();
});

test('only staff can access transactions (barang masuk & keluar)', function () {
    $this->actingAs($this->staff)->get(route('admin.transactions.index'))->assertSuccessful();
    $this->actingAs($this->staff)->get(route('admin.transactions.add'))->assertSuccessful();

    $this->actingAs($this->admin)->get(route('admin.transactions.index'))->assertForbidden();
    $this->actingAs($this->manager)->get(route('admin.transactions.index'))->assertForbidden();
});

test('only manager can access reports (laporan stok)', function () {
    $this->actingAs($this->manager)->get(route('admin.reports.index'))->assertSuccessful();

    $this->actingAs($this->admin)->get(route('admin.reports.index'))->assertForbidden();
    $this->actingAs($this->staff)->get(route('admin.reports.index'))->assertForbidden();
});

/*
|--------------------------------------------------------------------------
| ITEMS CRUD & STOCK MANAGEMENT TESTS
|--------------------------------------------------------------------------
*/

test('admin can create, update, and soft delete an item', function () {
    $uniqueCode = 'TEST-'.uniqid();

    // Create
    $response = $this->actingAs($this->admin)->post(route('admin.items.create'), [
        'code' => $uniqueCode,
        'name' => 'Barang Uji Coba',
        'category' => 'Uji Coba',
        'unit' => 'Pcs',
        'stock' => 25,
    ]);

    $response->assertRedirect(route('admin.items.index'));

    $item = DB::table(DatabaseConst::ITEM())
        ->where('code', $uniqueCode)
        ->first();

    expect($item)->not->toBeNull();
    expect($item->stock)->toBe(25);

    // Update
    $updateResponse = $this->actingAs($this->admin)->post(route('admin.items.doUpdate', $item->id), [
        'code' => $uniqueCode,
        'name' => 'Barang Uji Coba Diperbarui',
        'category' => 'Uji Coba 2',
        'unit' => 'Box',
    ]);

    $updateResponse->assertRedirect(route('admin.items.index'));

    $updatedItem = DB::table(DatabaseConst::ITEM())->where('id', $item->id)->first();
    expect($updatedItem->name)->toBe('Barang Uji Coba Diperbarui');
    expect($updatedItem->unit)->toBe('Box');

    // Delete (Soft delete)
    $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.items.delete', $item->id));
    $deleteResponse->assertRedirect(route('admin.items.index'));

    $deletedItem = DB::table(DatabaseConst::ITEM())->where('id', $item->id)->first();
    expect($deletedItem->deleted_at)->not->toBeNull();
    expect($deletedItem->deleted_by)->toBe($this->admin->id);
});

/*
|--------------------------------------------------------------------------
| TRANSACTIONS & AUTOMATIC STOCK LOGIC TESTS
|--------------------------------------------------------------------------
*/

test('staff can record barang masuk and stock increases automatically', function () {
    $uniqueCode = 'IN-'.uniqid();
    $itemId = DB::table(DatabaseConst::ITEM())->insertGetId([
        'code' => $uniqueCode,
        'name' => 'Item Masuk Test',
        'category' => 'Test',
        'unit' => 'Pcs',
        'stock' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($this->staff)->post(route('admin.transactions.create'), [
        'item_id' => $itemId,
        'type' => 'in',
        'quantity' => 15,
        'date' => date('Y-m-d'),
        'notes' => 'Barang masuk test',
    ]);

    $response->assertRedirect(route('admin.transactions.index'));

    $item = DB::table(DatabaseConst::ITEM())->where('id', $itemId)->first();
    expect($item->stock)->toBe(25); // 10 + 15
});

test('staff can record barang keluar and stock decreases automatically', function () {
    $uniqueCode = 'OUT-'.uniqid();
    $itemId = DB::table(DatabaseConst::ITEM())->insertGetId([
        'code' => $uniqueCode,
        'name' => 'Item Keluar Test',
        'category' => 'Test',
        'unit' => 'Pcs',
        'stock' => 30,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($this->staff)->post(route('admin.transactions.create'), [
        'item_id' => $itemId,
        'type' => 'out',
        'quantity' => 12,
        'date' => date('Y-m-d'),
        'notes' => 'Barang keluar test',
    ]);

    $response->assertRedirect(route('admin.transactions.index'));

    $item = DB::table(DatabaseConst::ITEM())->where('id', $itemId)->first();
    expect($item->stock)->toBe(18); // 30 - 12
});

test('staff cannot record barang keluar if quantity exceeds current stock', function () {
    $uniqueCode = 'FAIL-'.uniqid();
    $itemId = DB::table(DatabaseConst::ITEM())->insertGetId([
        'code' => $uniqueCode,
        'name' => 'Item Insufficient Test',
        'category' => 'Test',
        'unit' => 'Pcs',
        'stock' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($this->staff)->post(route('admin.transactions.create'), [
        'item_id' => $itemId,
        'type' => 'out',
        'quantity' => 10, // exceeds stock (5)
        'date' => date('Y-m-d'),
        'notes' => 'Barang keluar melebihi stok',
    ]);

    $response->assertSessionHas('error', 'Stok tidak mencukupi! Sisa stok saat ini: 5');

    // Stock must remain unchanged
    $item = DB::table(DatabaseConst::ITEM())->where('id', $itemId)->first();
    expect($item->stock)->toBe(5);
});

/*
|--------------------------------------------------------------------------
| REPORTS TESTS
|--------------------------------------------------------------------------
*/

test('manager can filter reports by date and type', function () {
    $response = $this->actingAs($this->manager)->get(route('admin.reports.index', [
        'start_date' => now()->subDays(7)->toDateString(),
        'end_date' => now()->toDateString(),
        'type' => 'in',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Laporan Stok');
    $response->assertSee('Ringkasan Posisi Stok Saat Ini');
});
