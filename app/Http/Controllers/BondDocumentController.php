<?php

namespace App\Http\Controllers;

use App\Http\Requests\BondDocument\CreateBondDocumentRequest;
use App\Http\Requests\BondDocument\IndexBondDocumentRequest;
use App\Http\Requests\BondDocument\ShowBondDocumentRequest;
use App\Http\Requests\BondDocument\StoreBondDocumentRequest;
use App\Services\BondDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Illuminate\View\View;

class BondDocumentController extends Controller
{
    public function __construct(private BondDocumentService $service)
    {
    }

    /**
     * @param IndexBondDocumentRequest $request
     *
     * @return View
     */
    public function index(IndexBondDocumentRequest $request): View
    {
        /**
         * @var string $sort
         */
        $sort = $request->query('sort') ?? '';

        /**
         * @var string $direction
         */
        $direction = $request->query('direction') ?? '';

        $documents = $this->service->list(sort: $sort, direction: $direction);

        return view('bond.document.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param CreateBondDocumentRequest $request
     *
     * @return View
     */
    public function create(CreateBondDocumentRequest $request): View
    {
        return view('bond.document.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBondDocumentRequest $request
     *
     * @return RedirectResponse
     */
    public function store(StoreBondDocumentRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('bondsDocuments.index')->with('success', 'Arquivo importado com sucesso.');
    }

    /**
     * Display the specified resource.
     *
     * @param ShowBondDocumentRequest $request
     * @param  int $id
     *
     * @return Response
     */
    public function show(ShowBondDocumentRequest $request, int $id): Response
    {
        $file = $this->service->getDocument($id);

        /**
         * @var string $data
         */
        $data = $file->get('data');

        /**
         * @var string $fileName
         */
        $fileName = $file->get('name');

        /**
         * @var string $mime
         */
        $mime = $file->get('mime');

        return FacadesResponse::make($data, 200, ['filename=' => $fileName])->header('Content-Type', $mime);
    }
}
