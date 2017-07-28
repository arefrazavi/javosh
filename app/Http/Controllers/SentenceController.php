<?php

namespace App\Http\Controllers;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
use App\Libraries\AspectLib;
use App\Libraries\SentenceLib;
use App\Libraries\SummaryLib;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Sentence;
use App\Models\Summary;
use App\Models\Type;
use App\Models\Word;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Null_;
use Yajra\Datatables\Datatables;

class SentenceController extends Controller
{
    public function viewList($commentId)
    {
        if ($commentId) {
            $comment = Comment::fetch($commentId);
            $product = Product::fetch($comment->product_id);
            $category = $product->category;
            $aspects = AspectLib::getAspects($category->id);
        }
        return view('sentence.list', compact('comment', 'aspects'));
    }

    public function getList(Request $request)
    {
        $this->validate($request, [
            'commentId' => 'required',
        ]);

        $comment = Comment::fetch($request->commentId);
        $product = Product::fetch($comment->product_id);
        //$sentences = $comment->sentences;
        $category = $product->category;
        $aspects = AspectLib::getAspects($category->id);

        $summaryData['method_id'] = Summary::GOLD_STANDARD_METHOD_ID;
        $summaryData['user_id'] = Sentinel::getUser()->id;
        $summaryData['product_id'] = $product->id;
        //$goldSummary = Summary::fetchProductSummary($summaryData);

        $sentences = SentenceLib::getSentencesWihSummary($comment->id, Sentinel::getUser()->id);

        foreach ($sentences as $sentence) {
            $sentence->aspects = $aspects;
        }

        return Datatables::of($sentences)->make(true);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function updateGoldSentences(Request $request)
    {
        $result = [];
        $this->validate($request, [
            'sentences' => 'required'
        ]);

        $sentencesArray = $request->sentences;

        $summaryData['method_id'] = Summary::GOLD_STANDARD_METHOD_ID;
        $summaryData['user_id'] = Sentinel::getUser()->id;

        foreach ($sentencesArray as $sentenceData) {
            $summaryData['sentence_id'] = intval($sentenceData[0]);
            $sentence = Sentence::fetch($summaryData['sentence_id']);
            $comment = $sentence->comment;
            $product = $comment->product;
            $summaryData['product_id'] = $product->id;
            $updateData['aspect_id'] = intval($sentenceData[1]);
            $updateData['polarity'] = intval($sentenceData[2]);

            SummaryLib::updateSummary($summaryData, $updateData);
        }

        $result['message'] = 'Gold standard summary has been successfully updated';

        return $result;
    }

    public function updateSentenceGoldStatus(Request $request)
    {
        $result['success'] = 1;
        $this->validate($request, [
            'goldRequest' => 'required',
        ]);

        $goldRequest = $request->goldRequest;


        $summaryData['method_id'] = Summary::GOLD_STANDARD_METHOD_ID;
        $summaryData['user_id'] = Sentinel::getUser()->id;
        $summaryData['sentence_id'] = intval($goldRequest['sentenceId']);
        $sentence = Sentence::fetch($summaryData['sentence_id']);
        $comment = $sentence->comment;
        $product = $comment->product;
        $summaryData['product_id'] = $product->id;

        if ($goldRequest['action'] == SummaryLib::ADD_TO_SUMMARY_ACTION) {
            $updateData['aspect_id'] = intval($goldRequest['aspectId']);
        }

        $updateData['polarity'] = intval($goldRequest['polarity']);
        $updateResult = SummaryLib::updateSummary($summaryData, $updateData);

        if (!$updateResult) {
            $result['success'] = 0;
            $result['message'] = trans("common_lang.Max_Summary_Size_Exceed");
        }

        return $result;
    }

    public function storeSentences(SentenceLib $sentenceLib)
    {
        $sentenceLib->storeSentences();
    }

    public function computeSentencesEntropy(SentenceLib $sentenceLib)
    {
        $sentenceLib->computeSentencesEntropy();
        print_r("\n********END******\n");

    }

    public function classifySentences(Request $request)
    {
        $this->validate($request, [
            'commentId' => 'required',
        ]);
    }

}
