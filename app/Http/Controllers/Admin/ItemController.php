<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\ItemUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    protected array $page = [
        'route' => 'items',
        'title' => 'Stok Barang',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected ItemUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    public function index(Request $request): View|Response
    {
        $data = $this->usecase->getAll([
            'keywords' => $request->get('keywords'),
        ]);
        $data = $data['data']['list'] ?? [];

        return view('_admin.items.index', [
            'data' => $data,
            'page' => $this->page,
            'keywords' => $request->get('keywords'),
        ]);
    }

    public function add(): View|Response
    {
        return view('_admin.items.add', [
            'page' => $this->page,
        ]);
    }

    public function doCreate(Request $request): RedirectResponse
    {
        $process = $this->usecase->create(data: $request);

        if ($process['success']) {
            return redirect()->route('admin.items.index')
                ->with('success', ResponseConst::SUCCESS_MESSAGE_CREATED);
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

        return view('_admin.items.detail', [
            'data' => (object) $data['data'],
            'page' => $this->page,
        ]);
    }

    public function update(int $id): View|RedirectResponse|Response
    {
        $data = $this->usecase->getByID($id);

        if (empty($data['data'])) {
            return redirect()->intended($this->baseRedirect)
                ->with('error', ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return view('_admin.items.update', [
            'data' => (object) $data['data'],
            'page' => $this->page,
        ]);
    }

    public function doUpdate(Request $request, int $id): RedirectResponse
    {
        $process = $this->usecase->update(data: $request, id: $id);

        if ($process['success']) {
            return redirect()->route('admin.items.index')
                ->with('success', ResponseConst::SUCCESS_MESSAGE_UPDATED);
        }

        return redirect()->back()->withInput()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    public function delete(int $id): RedirectResponse
    {
        $process = $this->usecase->delete($id);

        if ($process['success']) {
            return redirect()->route('admin.items.index')
                ->with('success', ResponseConst::SUCCESS_MESSAGE_DELETED);
        }

        return redirect()->route('admin.items.index')
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }
}
