<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicationSearchRequest;
use App\Http\Resources\MedicationResource;
use App\Models\Medication;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MedicationController extends Controller
{
    public function search(MedicationSearchRequest $request): AnonymousResourceCollection
    {
        $medications = Medication::lot($request->validated('lot'))->get();

        return MedicationResource::collection($medications);
    }
}
