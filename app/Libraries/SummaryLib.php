<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
use App\Models\Aspect;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Result;
use App\Models\Sentence;
use App\Models\Summary;
use App\Models\User;
use App\Models\Word;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class SummaryLib
{
    const REMOVE_FROM_SUMMARY_ACTION = 0;
    const ADD_TO_SUMMARY_ACTION = 1;
    const MAX_SUMMARY_SIZE = 10;

    /**
     * @param $summaryData
     * @param $updateData
     * @return bool
     */
    public static function updateSummary($summaryData, $updateData)
    {

        if (isset($updateData['aspect_id']) && $updateData['aspect_id']) {
            $data = $summaryData;
            $data['aspect_id'] = $updateData['aspect_id'];
            if (!self::validateGoldSummaryAddition($data)) {
                return false;
            }
            Summary::updateOrInsert($summaryData, $updateData);
        } else {
            $whereRaw = "method_id = " . $summaryData['method_id'] . " AND " .
                "user_id = " . $summaryData['user_id'] . " AND ".
                "product_id = " . $summaryData['product_id'] . " AND " .
                "sentence_id = " . $summaryData['sentence_id'];
            Summary::deleteSummary($whereRaw);
        }

        return true;
    }

    public static function storeSummaries($filePaths)
    {
        foreach ($filePaths as $filePath) {
            $files = glob($filePath['filePath']);
            foreach ($files as $fileName) {
                $summary = Common::readFromCsv($fileName);
                unset($summary[0]);
                $summaryData['method_id'] = $filePath['method_id'];
                $summaryData['user_id'] = User::ADMIN_USER_ID;
                $summaryData['product_id'] = intval(basename($fileName, "csv"));

                $whereRaw = "product_id = " . $summaryData['product_id'] . " AND user_id = " . $summaryData['user_id'] . " AND method_id = " . $summaryData['method_id'];
                Summary::deleteSummary($whereRaw); //Delete previous summary of the product in the method if exists

                foreach ($summary as &$aspectSummary) {
                    $updateData['aspect_id'] = $aspectSummary[0]; // first field of each row contains aspect id
                    unset($aspectSummary[0]);

                    print_r("Aspect: " . $updateData['aspect_id'] . "\n");

                    foreach ($aspectSummary as $sentenceId) {
                        if (intval($sentenceId)) {
                            $summaryData['sentence_id'] = intval($sentenceId);
                            $updateData['polarity'] = 0;
                            print_r("Sentence: " . $summaryData['sentence_id'] . "\n");

                            $whereRaw = "sentence_id = " . $summaryData['sentence_id'] .
                                " AND user_id = " . $summaryData['user_id'] . " AND product_id = " . $summaryData['product_id'] .
                                " AND method_id = " . $summaryData['method_id'];
                            if (!Summary::fetch($whereRaw)) {
                                Summary::updateOrInsert($summaryData, $updateData);
                            }
                        }
                    }
                }
                unset($aspectSummary);
            }
        }

        print_r("*** End of  storeSummaries *** \n");
    }

    public static function getRecommendedSentences($productId, $aspectId)
    {
        $recommendedSentences = [];
        //$product = Product::fetch($productId);
        $comments = Comment::fetchComments("id, text", "product_id = $productId");
        if (!$comments->count()) {
            return $recommendedSentences;
        }
        $firstSentence = $comments{0}->sentences{0};
        $firstSentence->aspect_frequency = unserialize($firstSentence->aspect_frequency);
        $firstSentence->weighted_aspect_freq = 0;
        if ($firstSentence->aspect_frequency && isset($firstSentence->aspect_frequency[$aspectId])) {
            $firstSentence->weighted_aspect_freq = $firstSentence->aspect_frequency[$aspectId];
        }
        $recommendedSentences[0] = $firstSentence;
        unset($comments{0}->sentences{0});
        $i = 1;
        foreach ($comments as $comment) {
            //$sentences = $comment->sentences;
            $selectRaw = "sentences.id, text, aspect_frequency, aspect_id, polarity";
            $whereRaw = 'comment_id = '. $comment->id .' AND ' . '(user_id = '. Sentinel::getUser()->id . " OR user_id IS NULL) 
        AND (method_id = ". Summary::GOLD_STANDARD_METHOD_ID . ' OR  method_id IS NULL)';
            $sentences = Sentence::fetchSentencesWithSummary($selectRaw, $whereRaw);
            foreach ($sentences as $sentence) {
                $aspectFrequency = unserialize($sentence->aspect_frequency);
                if (!isset($aspectFrequency[$aspectId])) {
                    $sentence->weighted_aspect_freq = 0;
                } else if (!$aspectFrequency[$aspectId]) {
                    continue;
                } else {
                    $sentence->weighted_aspect_freq = $aspectFrequency[$aspectId];
                }
                $sentence->comment_text = $comment->text;
                $recommendedSentences[] = $sentence;

                /*Sorting based on by datatables is faster*/
//                $k = $i;
//                $j = $i - 1;
//                if ($recommendedSentences[$j]->aspect_frequency[$aspectId] >= $aspectFrequency) {
//                    $recommendedSentences[$i] = $sentence;
//                } else {
//                    while ($j >= 0 && ($recommendedSentences[$j]->aspect_frequency[$aspectId] < $sentence->aspect_frequency[$aspectId])) {
//                        $tempSentence = $recommendedSentences[$j];
//                        $recommendedSentences[$j] = $sentence;
//                        $recommendedSentences[$k] = $tempSentence;
//                        $k--;
//                        $j--;
//                    }
//                }
//                $i++;
            }
        }

        return $recommendedSentences;
    }



    public static function validateGoldSummaryAddition($summaryData)
    {
        $whereRaw = "method_id = " . $summaryData['method_id'] . " AND " .
            "user_id = " . $summaryData['user_id'] . " AND ".
            "product_id = " . $summaryData['product_id'] . " AND ".
            "aspect_id = " . $summaryData['aspect_id'];

        $summarySentences = Summary::fetchSummary($whereRaw);

        if ($summarySentences->count() >= SummaryLib::MAX_SUMMARY_SIZE) {
            return false;
        }

        return true;
    }
}
