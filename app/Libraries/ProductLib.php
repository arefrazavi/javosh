<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Models\Aspect;
use App\Models\FileLog;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Type;

class ProductLib
{

    public static function storeProducts($files)
    {
        $productType = Type::fetch('product');
        $aspectsType = Type::fetch('aspects');

        foreach ($files as $file) {
            foreach ($file['filePaths'] as $filePath) {
                $fileName = basename($filePath);
//                if (!empty(FileLog::fetch($fileName))) {
//                    print_r("File $fileName has been already inserted \n");
//                    continue;
//                }

                print_r("***** Inserting products of category " . $file['categoryId'] . " \n");
                $rows = Common::readFromCsv($filePath);
                $titles = $rows[0];
                unset($rows[0]);
                $aspectIds = [];

                $aspectData['category_id'] = $file['categoryId'];
                for ($i = 6; $i < sizeof($titles); $i++) {
                    $aspectData['title'] = trim($titles[$i]);
                    $aspect = Aspect::updateOrInsert($aspectData);
                    $aspectIds[$i] = $aspect->id;
                }

                foreach ($rows as $row) {
                    $productData['id'] = intval($row[0]);
                    $productData['category_id'] = $file['categoryId'];

                    $price = str_replace(',', '', $row[2]);
                    if (!$price) {
                        $price = 0;
                    }

                    $updateData['title'] = trim($row[1]);
                    $updateData['price'] = doubleval($price);
                    $updateData['recommendationCount'] = intval($row[4]);
                    $updateData['description'] = trim($row[5]);

                    $product = Product::updateOrInsert($productData, $updateData);

                    print_r("Product " . $product->id . " has been inserted/updated \n");

                    /**Saving ratings related to the product**/
                    $ratingData['entity_id'] = $product->id;
                    $ratingData['entity_type_id'] = $productType->id;
                    $ratingData['rating_type_id'] = $aspectsType->id;
                    $rates = [];

                    $rating = Rating::fetch($ratingData);
                    if ($rating) {
                        print_r("Rating for product " . $rating->entity_id . " has already inserted \n");
                        continue;
                    }
                    for ($i = 6; $i < sizeof($row); $i++) {
                        if (isset($row[$i])) {
                            $rates[$aspectIds[$i]] = intval($row[$i]);
                        } else {
                            $rates[$aspectIds[$i]] = 0;
                        }
                    }

                    $updateData['rate'] = serialize($rates);
                    $rating = Rating::updateOrInsert($ratingData, $updateData);

                    print_r("Rating for product " . $rating->entity_id . " has been inserted/updated \n");
                }

                //Save file name in log table to avoid reading again
                //$fileLooData['file_name'] = $fileName;
                //$fileLog = FileLog::insert($fileLooData);
                //print_r("File " . $fileLog->id . " has been inserted/updated \n");
            }

        }
    }


    public static function SaveProductsIntoFile() {
        $whereRaw = "title = ''";
        $products = Product::fetchProducts("*", $whereRaw);
        $uncrawledProducts = [];
        foreach ($products as $product) {
            $uncrawledProducts[] = ['id' => $product->id, 'categoryTitle' =>  $product->category->title];
        }

        $filePath = base_path('data/products/uncrawled-products.csv');
        $writingMode = 'w';
        Common::writeToCsv($uncrawledProducts, $filePath, $writingMode);


    }


}