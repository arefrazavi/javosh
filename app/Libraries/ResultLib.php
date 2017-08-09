<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Models\Category;
use App\Models\Product;
use App\Models\Result;
use App\Models\Summary;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResultLib
{
    public static function evaluateResults()
    {
        DB::table('evaluation_results')->truncate();
        $whereClause = "id <> " . Summary::GOLD_STANDARD_METHOD_ID;
        $methods = Summary::fetchMethods("*", $whereClause);
        $categories = Category::fetchCategories();
        $results = [];

        $measures = Result::fetchEvaluationMeasures();

        foreach ($categories as &$category) {
            $products = $category->products;
            $productCount = $products->count();
            print_r(" $productCount Products in category: $category->id \n");
            if (!$productCount) {
                continue;
            }
            $aspects = AspectLib::getAspects($category->id);

            foreach ($products as &$product) {

                foreach ($aspects as &$aspect) {

                    //fetch gold summaries
                    $whereClause = "method_id = " . Summary::GOLD_STANDARD_METHOD_ID . " AND product_id = $product->id AND aspect_id = $aspect->id";
                    $selectClause = "sentence_id, COUNT(*) AS sentence_count";
                    $goldSResults = [];
                    $aspectSummaries = Summary::fetchSummaries($selectClause, $whereClause, Summary::MAX_SUMMARY_SIZE, 0,
                        "sentence_count", "DESC", "sentence_id");

                    $goldCount = $aspectSummaries->count();

                    if (!$goldCount) {
                        print_r(" No gold summary for product $product->id in aspect $aspect->id \n");
                        continue;
                    }

                    foreach ($aspectSummaries as $goldSummary) {
                        $goldSResults[$goldSummary->sentence_id] = $goldSummary->sentence_count;
                    }

                    foreach ($methods as &$method) {
                        $whereClause = "method_id = $method->id AND product_id = $product->id AND aspect_id = $aspect->id";
                        $selectClause = "sentence_id, COUNT(*) AS sentence_count";
                        $aspectSummaries = Summary::fetchSummaries($selectClause, $whereClause, Summary::MAX_SUMMARY_SIZE, 0,
                            "sentence_count", "DESC", "sentence_id");

                        $datasetCount = $aspectSummaries->count();

                        if (!$datasetCount) {
                            print_r(" No summary for product $product->id in aspect $aspect->id in method $method->id \n");
                            continue;
                        }

                        $correctCount = 0;
                        foreach ($aspectSummaries as $aspectSummary) {
                            if (isset($goldSResults[$aspectSummary->sentence_id])) {
                                $correctCount++;
                                print_r("     correct count $correctCount \n");
                            }
                        }
                        $precision = $correctCount / $datasetCount;
                        $recall = $correctCount / $goldCount;
                        $fMeasure = ($precision || $recall) ? 2 * ($precision * $recall) / ($precision + $recall) : 0;

                        if (isset($results[$category->id][$aspect->id][$method->id])) {
                            $results[$category->id][$aspect->id][$method->id][Result::PRECISION_MEASURE_ID]->result += $precision;
                            $results[$category->id][$aspect->id][$method->id][Result::PRECISION_MEASURE_ID]->count++;
                            $results[$category->id][$aspect->id][$method->id][Result::RECALL_MEASURE_ID]->result += $recall;
                            $results[$category->id][$aspect->id][$method->id][Result::RECALL_MEASURE_ID]->count++;
                            $results[$category->id][$aspect->id][$method->id][Result::F_MEASURE_ID]->result += $fMeasure;
                            $results[$category->id][$aspect->id][$method->id][Result::F_MEASURE_ID]->count++;
                        } else {
                            foreach ($measures as &$measure) {
                                $result = new Result();
                                $result->category_id = $category->id;
                                $result->method_id = $method->id;
                                $result->aspect_id = $aspect->id;
                                $result->measure_id = $measure->id;
                                $result->result = 0;
                                $result->count = 0;
                                $results[$category->id][$aspect->id][$method->id][$measure->id] = $result;
                            }
                            unset($measure);
                        }

                        print_r("\n precision: $precision, recall $recall\n");
                    }
                    unset($method);
                }
                unset($aspect);
            }
            unset($product);
        }
        unset($category);

        foreach ($results as &$categoryResults) {
            foreach ($categoryResults as &$aspectResults) {
                foreach ($aspectResults as &$methodResults) {
                    foreach ($methodResults as &$measureResult) {
                        if ($measureResult->count) {
                            $measureResult->result = $measureResult->result / $measureResult->count;
                            $measureResult->save();
                            print_r("\n Result for category $measureResult->category_id, aspect  $measureResult->aspect_id: measure $measureResult->measure_id ===> $measureResult->result  \n");
                        }
                    }
                    unset($measureResult);
                }
                unset($methodResults);
            }
            unset($aspectResults);
        }
        unset($categoryResults);

        print_r("*** End of evaluating results *** \n");
    }

    public static function storeResults($fileDir)
    {
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