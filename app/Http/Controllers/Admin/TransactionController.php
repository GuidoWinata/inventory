<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\TransactionUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    protected array $page = [
        'route' => 'transactions',
        'title' => 'Barang Masuk & Keluar',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected TransactionUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    public function index(Request $request): View|Response
    {
        $data = $this->usecase->getAll([
            'keywords' => $request->get('keywords'),
            'type' => $request->get('type', 'all'),
        ]);
        $data = $data['data']['list'] ?? [];

        return view('_admin.transactions.index', [
            'data' => $data,
            'page' => $this->page,
            'keywords' => $request->get('keywords'),
            'type' => $request->get('type', 'all'),
        ]);
    }

    public function add(): View|Response
    {
        $items = $this->usecase->getItemsForDropdown();
        $items = $items['data'] ?? [];

        return view('_admin.transactions.add', [
            'items' => $items,
            'page' => $this->page,
        ]);
    }

    public function doCreate(Request $request): RedirectResponse
    {
        $process = $this->usecase->create(data: $request);

        if ($process['success']) {
            return redirect()->route('admin.transactions.index')
                ->with('success', $process['message'] ?? ResponseConst::SUCCESS_MESSAGE_CREATED);
        }

        return redirect()->back()->withInput()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    public function detail(int $id): View|RedirectResponse|Response
    {
        $data = $this->usecase->getByID($id);

        if (empty($data['data'])) {
            return redirect()->intended($this->baseRedirect)
                ->with('error', ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return view('_admin.transactions.detail', [
            'data' => (object) $data['data'],
            'page' => $this->page,
        ]);
    }
}
