<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bond\ReviewBondRequest;
use App\Models\Bond;
use App\Services\BondService;
use Illuminate\Http\RedirectResponse;

class ReviewBond extends Controller
{
    public function __construct(private BondService $service)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ReviewBondRequest  $request
     * @param  Bond  $bond
     *
     * @return RedirectResponse
     */
    public function __invoke(ReviewBondRequest $request, Bond $bond): RedirectResponse
    {
        try {
            $this->service->review($request->validated(), $bond);
        } catch (\Exception $e) {
            return redirect()->route('bonds.show', $bond)->withErrors(['noStore' => 'Não foi possível salvar o vínculo: ' . $e->getMessage()]);
        }

        return redirect()->route('bonds.show', $bond)->with('success', 'Vínculo atualizado com sucesso.');
    }
}
