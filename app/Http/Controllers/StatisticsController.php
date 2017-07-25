<?php

namespace App\Http\Controllers;

use App\Libraries\AspectLib;
use App\Models\Category;
use App\Models\Result;
use App\Models\Summary;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function viewResults()
    {
        $charts = [];
        $categories = Category::fetchCategories();
        $measures = Result::fetchEvaluationMeasures();
        $methods = Summary::fetchMethods();
        $methodColors = [];
        foreach ($methods as $method) {
            $color = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
            $methodColors[$method->id] = $color;
        }

        foreach ($categories as $category) {
            $whereClause = 'category_id ='. $category->id;
            $results = Result::fetchResults($whereClause);
            if (!$results->count()) {
                continue;
            }

            $aspects = AspectLib::getAspects($category->id);
            $aspectTitles = [];
            foreach ($aspects as $aspect) {
                $aspectTitles[$aspect->id] = $aspect->title;
            }
            $aspectsSize =  sizeof($aspects);

            foreach ($measures as $measure) {
                $resultData['measure_id'] = $measure->id;
                $datasets = [];
                foreach ($methods as $method) {
                    $whereClause = 'category_id = ' . $category->id . ' AND measure_id = '
                        . $measure->id . ' AND method_id = ' . $method->id;
                    $results = Result::fetchResults($whereClause);

                    if (!$results->count()) {
                        continue;
                    }

                    $data = [];
                    foreach ($results as $result) {
                        $data[$result->aspect_id] = $result->result;
                    }

                    $dataset = [
                        "label" => $method->title,
                        'backgroundColor' => array_fill(0, $aspectsSize, $methodColors[$method->id]),
                        'data' => $data
                    ];
                    $datasets[] = $dataset;
                }

                if (empty($datasets)) {
                    continue;
                }

                $charts[$category->id]['category'] = $category->alias;
                $charts[$category->id]['aspectTitles'] = $aspectTitles;
                $charts[$category->id]['measures'][$measure->id]['chartId'] = $category->id.$measure->id;
                $charts[$category->id]['measures'][$measure->id]['measureTitle'] = $measure->title;
                $charts[$category->id]['measures'][$measure->id]['datasets'] = $datasets;
            }

        }

        return view('Statistics.evaluation_results', compact('charts'));
    }
}
