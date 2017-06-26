<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Models\Category;
use App\Models\Result;
use App\Models\Summary;
use App\Models\User;

class ResultLib
{
    public static function evaluateResults($methodId)
    {
        $categories = Category::fetchCategories();
        $results = [];

        foreach ($categories as $category) {
            $products = $category->products;
            $productCount = $products->count();
            print_r(" $productCount Products in category: $category->id \n");
            if (!$productCount) {
                continue;
            }

            $aspects = AspectLib::getAspects($category->id);
            $evaluatedProductsNum = 0;
            foreach ($products as $product) {
                $whereRaw = 'product_id = ' . $product->id  . ' AND user_id = ' . User::ADMIN_USER_ID;
                $summary = Summary::fetchSummary($whereRaw);

                //If there is no summary for product, it must not be evaluated
                if (!$summary->count()) {
                    print_r(  " No summary for product $product->id \n");
                    continue;
                }
                foreach ($aspects as $aspect) {
                    $whereRaw .= ' AND aspect_id = ' . $aspect->id;

                    $goldWhereRaw = $whereRaw . ' AND method_id = ' . Summary::GOLD_STANDARD_METHOD_ID;
                    $goldAspectSummary = Summary::fetchSummary($goldWhereRaw);

                    $methodWhereRaw = $whereRaw . ' AND method_id = ' . $methodId;
                    $methodAspectSummary = Summary::fetchSummary($methodWhereRaw);

                    //Calculate Precision, Recall and F-Measure for each aspect
                    $matchedNum = 0;
                    $goldSummarySize = sizeof($goldAspectSummary);
                    $methodSummarySize = sizeof($methodAspectSummary);

                    $goldSentencesIds = [];
                    foreach ($goldAspectSummary as $goldSentence) {
                        $goldSentencesIds[$goldSentence->sentence_id] = $goldSentence->sentence_id;
                    }
                    foreach ($methodAspectSummary as $methodSentence) {
                        if (isset($goldSentencesIds[$methodSentence->sentence_id])) {
                            $matchedNum++;
                        }
                    }

                    if ($methodSummarySize == 0 && $goldSummarySize == 0) {
                        $precision = 1;
                        $recall = 1;
                        $fMeasure = 1;
                    } else {
                        $precision = ($methodSummarySize) ? ($matchedNum / $methodSummarySize) : 0;
                        $recall = ($goldSummarySize) ? ($matchedNum / $goldSummarySize) : 0;
                        $fMeasure = ($precision || $recall) ? 2 * ($precision * $recall) / ($precision + $recall) : 0;
                    }

                    if (isset($results[$category->id][$aspect->id])) {
                        $results[$category->id][$aspect->id][Result::PRECISION_MEASURE_ID] += $precision;
                        $results[$category->id][$aspect->id][Result::RECALL_MEASURE_ID] += $recall;
                        $results[$category->id][$aspect->id][Result::F_MEASURE_ID] += $fMeasure;
                    } else {
                        $results[$category->id][$aspect->id][Result::PRECISION_MEASURE_ID] = $precision;
                        $results[$category->id][$aspect->id][Result::RECALL_MEASURE_ID] = $recall;
                        $results[$category->id][$aspect->id][Result::F_MEASURE_ID] = $fMeasure;

                    }
                    $evaluatedProductsNum++;

                    print_r(  "Precision: $precision and Recall: $recall in Aspect $aspect->id of Product $product->id \n");

                }
            }

            if (!$evaluatedProductsNum) {
                print_r("Category $category->id doesn't have any product with summary \n");
                continue;
            }

            foreach ($aspects as $aspect) {
                $results[$category->id][$aspect->id][Result::PRECISION_MEASURE_ID] /= $evaluatedProductsNum;
                $results[$category->id][$aspect->id][Result::RECALL_MEASURE_ID] /= $evaluatedProductsNum;
                $results[$category->id][$aspect->id][Result::F_MEASURE_ID] /= $evaluatedProductsNum;
            }
        }

        dump($results);

        $resultData['method_id'] = $methodId;
        foreach ($results as $categoryId => $aspectResults) {
            $resultData['category_id'] = $categoryId;
            foreach ($aspectResults as $aspectId => $aspectResult) {
                $resultData['aspect_id'] = $aspectId;
                foreach ($aspectResult as $measureId => $measureResult) {
                    $resultData['measure_id'] = $measureId;
                    $updateData = ['result' => $measureResult];
                    Result::updateOrInsert($resultData, $updateData);

                    print_r(  " Result for aspect $aspectId of category $categoryId in measure $measureId has been updated\n");
                }
            }

        }

        print_r("*** End of evaluateResults *** \n");
    }

    public static function storeResults($fileDir) {
        $files = glob($fileDir);
        foreach ($files as $fileName) {
            $results = Common::readFromCsv($fileName);
            $fields = $results[0];
            unset($results[0]);
            foreach ($results as $result) {
                $resultData['method_id'] = $result[array_search('method_id', $fields)];
                $resultData['category_id'] = $result[array_search('category_id', $fields)];
                $resultData['measure_id'] = $result[array_search('measure_id', $fields)];
                $resultData['aspect_id'] = $result[array_search('aspect_id', $fields)];
                $updateData['result'] = doubleval($result[array_search('result', $fields)]);

                Result::updateOrInsert($resultData, $updateData);

                print_r($updateData['result'] . "\n");
            }
        }

    }


}