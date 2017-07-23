<?php

namespace App\Http\Controllers;

use App\Helpers\Common;
use App\Libraries\AspectLib;
use App\Libraries\SummaryLib;
use App\Models\Aspect;
use App\Models\Category;
use App\Models\FileLog;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Summary;
use App\Models\Type;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Faker\Provider\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class ProductController extends Controller
{
    public function viewList($categoryId = 0)
    {
        $category = Category::fetch($categoryId);
        return view('product.list', compact('category', 'categoryId'));
    }

    public function getList(Request $request)
    {
        $whereRaw = "1";
        if ($request->categoryId) {
            $categoryId = $request->categoryId;
            $whereRaw = "category_id IN (". $categoryId;
            $descendants = Category::fetchDescendants($categoryId);
            foreach ($descendants as $descendant) {
                $whereRaw .= ", $descendant->id";
            }
            $whereRaw .= ")";
        }

        $products = Product::fetchProducts("*", $whereRaw);
        foreach ($products as &$product) {
            $category = $product->category;
            $product->category = $category;
        }
        unset($product);

        return Datatables::of($products)->make(true);
    }

    public function viewProduct($productId)
    {
        $product = Product::fetch($productId);
        $aspects = AspectLib::getAspects($product->category_id);
        $summarizationMethods = Summary::fetchMethods();

        //Get Summaries of different method for each aspect
        $summaryData['product_id'] = $productId;
        $summaryData['user_id'] = Sentinel::getUser()->id;
        $summaries = [];
        foreach ($aspects as $aspect) {
            $keywordsText = '';
            $keywords = $aspect->keywords;
            if ($keywords) {
                foreach ($keywords as $keyword => $weight) {
                    $keywordsText .= $keyword . "، ";
                }
            }
            $aspect->keywords = $keywordsText;
            $summaryData['aspect_id'] = $aspect->id;
            foreach ($summarizationMethods as $summarizationMethod) {
                $summaryData['method_id'] = $summarizationMethod->id;
                $summary = SummaryLib::getProductSummary($summaryData);

                if (!empty($summary)) {
                    $summaries[$summaryData['aspect_id']][$summaryData['method_id']]['method'] = $summarizationMethod;
                    $summaries[$summaryData['aspect_id']][$summaryData['method_id']]['summary'] = $summary;
                }
            }

        }


        return view("product.product", compact('product','summaries', 'aspects'));
    }

    public function viewUploadPanel(){
        return view('product.upload-panel');
    }

    public function upload(Input $input)
    {
        $results = [];
        $files = $input::file('files');
        $requiredValidator = Validator::make(
            ['files' => $files],
            ['files' => 'required']
        );

        if ($requiredValidator->fails()) {
            $results['errors'] = $requiredValidator->errors()->all();
        } else {
            $results = [];
            $results['errors'] = [];
            $results['success'] = [];
            $category = Category::fetch(0, 'Mobile-Phone');
            $aspectIds = AspectLib::retrieveAspectIds($category->id);
            $productType = Type::fetch('product');
            $aspectsType = Type::fetch('aspects');

            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();

                $extensionValidator = Validator::make(
                    ['extension' => strtolower($file->getClientOriginalExtension())],
                    ['extension' => 'required|in:csv']
                );

                if ($extensionValidator->passes()) {
                    if (!empty(FileLog::fetch($fileName))) {
                        $results['success'][] = [
                            0,
                            'File ' . $fileName . ' is already inserted'
                        ];
                        continue;
                    }

                    if (($handle = fopen($file->getPathName(), "r")) !== false) {
                        $titlesRow = fgetcsv($handle); //get header row
                        while (($row = fgetcsv($handle)) !== false) {
                            //Saving Product
                            $productId = $row[0];
                            print_r("\n<br>".$productId."<b>\n");
                            if ($productId == 128868) {
                                $test = 'what';
                            }
                            $product = Product::fetch($productId);
                            if (!$product) {
                                $product = new Product();
                                $product->id = $productId;
                            }
                            $product->title = $row[1];
                            $price = str_replace(',', '', $row[2]);
                            if (!$price) {
                                $price = 0;
                            }
                            $product->price = $price;
                            $product->recommendation_count = $row[4];
                            $product->description = $row[5];
                            $product->category_id = $category->id;
                            $product->save();

                            /**Saving ratings related to the comment**/
                            $newRating['entity_id'] = $product->id;
                            $newRating['entity_type_id'] = $productType->id;
                            $newRating['rating_type_id'] = $aspectsType->id;
                            $aspectIndex = 6;
                            $rates = [];
                            foreach ($aspectIds as $aspectId) {
                                $rates[$aspectId] = intval($row[$aspectIndex]);
                                $aspectIndex++;
                            }
                            $newRating['rate'] = serialize($rates);
                            $rating = Rating::fetch($newRating);
                            if (!$rating) {
                                Rating::insert($newRating);
                            } else {
                                Rating::updateRate($newRating);
                            }

                        }
                    }

                    //Save file name in log table to avoid reading again
                    $newFile['file_name'] = $fileName;
                    FileLog::insert($newFile);
                    $results['success'][] = [
                        1,
                        'File ' . $fileName . ' is successfully inserted'
                    ];
                } else {
                    $results['errors'] = $extensionValidator->errors()->all();
                }

                return redirect(route('ProductController.viewUploadPanel'))->with('results', $results);
            }
        }
    }

    public function viewGoldSummaryRecommendation($productId)
    {
        
//        $recommendedSentences = SummaryLib::getRecommendedSentences(81294, 1);
//        foreach($recommendedSentences as $recommendedSentence) {
//            echo $recommendedSentence->text . "<br>";
//            echo $recommendedSentence->aspect_frequency[1] . "<br>";
//            echo "-------<br>";
//        }
//        return;

        $product = Product::fetch($productId);
        $aspects = $product->category->aspects;
        return view('product.gold-summary-recommendation', compact('product', 'aspects'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getGoldSummaryRecommendation(Request $request)
    {
        $this->validate($request, [
            'productId' => 'required|integer',
            'aspectId' => 'required|integer',
        ]);

        $recommendedSentences = SummaryLib::getRecommendedSentences($request->productId, $request->aspectId);

        return Datatables::of($recommendedSentences)->make(true);
    }

}
