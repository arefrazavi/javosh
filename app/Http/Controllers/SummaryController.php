<?php

namespace App\Http\Controllers;

use App\Libraries\SummaryLib;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function getList(Request $request)
    {
        $this->validate($request, [
            'aspectId' => 'required',
            'productId' => 'required',
            'methodId' => 'required',
        ]);


        $summaryData['aspect_id'] = intval($request->aspectId);
        $summaryData['method_id'] = intval($request->methodId);
        $summaryData['product_id'] = intval($request->productId);

        $goldSummary = SummaryLib::getProductSummary($summaryData);

        return $goldSummary;
    }
}
