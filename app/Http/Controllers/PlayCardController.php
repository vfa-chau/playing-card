<?php

namespace App\Http\Controllers;

use App\Services\PlayCardService;
use App\Http\Requests\DistributeCardRequest;
use Illuminate\Support\Facades\Log;

class PlayCardController extends Controller
{
    protected $playCardService;

    public function __construct(PlayCardService $playCardService)
    {
        $this->playCardService = $playCardService;
    }

    public function distribute(DistributeCardRequest $request)
    {
        try {
            $result = $this->playCardService->getDistributedCards(
                $request->number_of_player
            );

            return view('home', [
                'distributedCards' => $result['distributedCards'],
                'remainedCards' => $result['remainedCards'],
                'distributedCardTotal' => $result['distributedCardTotal']
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return back()->withInput()->withErrors(['error' => __('validation.custom.error')]);
        }
    }
}
