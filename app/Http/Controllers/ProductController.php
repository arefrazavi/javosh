<?php

namespace App\Http\Controllers;

use App\Helpers\Common;
use App\Libraries\AspectLib;
use App\Libraries\ProductLib;
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
    public function viewList($categoryId = 0, $limit = 0)
    {
        $category = Category::fetch($categoryId);
        if ($limit) {
            return view('product.lucky-list', compact('category', 'categoryId', 'limit'));
        } else {
            return view('product.list', compact('category', 'categoryId'));
        }
    }

    public function getList(Request $request)
    {
        $categoryId = 0;
        $limit = 0;
        if ($request->categoryId) {
            $categoryId = $request->categoryId;
        }
        if ($request->limit) {
            $limit = $request->limit;
            if ($request->session()->has('luckyProducts')) {
                $products = session('luckyProducts');
            } else {
                $products = ProductLib::getProducts($categoryId, $limit);
                foreach ($products  as $index => $product) {
                    $product->index = $index;
                }
                session(['luckyProducts' => $products]);
            }
        } else {
            $products = ProductLib::getProducts($categoryId, $limit);
        }

        return Datatables::of($products)->make(true);
    }

    public function viewGoldSummaryRecommendation($productId, $isLucky = 0)
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
        foreach ($aspects as &$aspect) {
            $aspect->keywords = unserialize($aspect->keywords);
        }
        unset($aspect);

        $nextLuckyProduct = null;
        if ($isLucky) {
            $luckyProducts = session('luckyProducts');
            foreach ($luckyProducts as $index => &$luckyProduct) {
                if ($luckyProduct->id == $productId) {
                    if (isset($luckyProducts{$index + 1})) {
                        $nextLuckyProduct = $luckyProducts{$index + 1};
                    }
                    if (isset($luckyProducts{$index - 1})) {
                        $previousLuckyProduct = $luckyProducts{$index - 1};
                    }
                }
            }
            unset($luckyProduct);
        }

        return view('product.gold-summary-recommendation', compact('product', 'aspects', 'nextLuckyProduct', 'previousLuckyProduct'));
    }

    public function viewProduct($productId, $isLucky = 0)
    {
        $product = Product::fetch($productId);
        $aspects = AspectLib::getAspects($product->category_id);
        if (!$product->title) {
            $category = $product->category;
            $product->title = trans("common_lang.Productofcategory") . " " . $category->alias;
        }
        $whereClause = "id <> " . Summary::GOLD_STANDARD_METHOD_ID;
        $methods = Summary::fetchMethods("*", $whereClause);

        //Get Summaries of different method for each aspect
        $summaryData['product_id'] = $productId;
        $goldSummaries = [];
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
            $summaryData['method_id'] = Summary::GOLD_STANDARD_METHOD_ID;
            $goldSummary = SummaryLib::getProductSummary($summaryData);
            $goldSummaries[$summaryData['aspect_id']] = $goldSummary;

        }

        return view("product.product", compact('product', 'goldSummaries', 'aspects', 'methods'));
    }

    public function viewUploadPanel()
    {
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
                            print_r("\n<br>" . $productId . "<b>\n");
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
