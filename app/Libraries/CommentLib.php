<?php

namespace App\Libraries;

use App\Helpers\Common;
use App\Helpers\Tokenizer;
use App\Models\Aspect;
use App\Models\Category;
use App\Models\Comment;
use App\Models\FileLog;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Result;
use App\Models\Summary;
use App\Models\Type;
use App\Models\User;

class CommentLib
{

    public static function storeComments($files)
    {

        $commentType = Type::fetch('comment');
        $likeType = Type::fetch('like');
        $dislikeType = Type::fetch('dislike');
        $aspectsType = Type::fetch('aspects');
        foreach ($files as $file) {
            $commentData['product_id'] = $file['categoryId'];
            $category = Category::fetch($file['categoryId']);
            $aspects = $category->aspects;

            foreach ($file['filePaths'] as $filePath) {
                $fileName = basename($filePath);
                if (!empty(FileLog::fetch($fileName))) {
                    print_r("File $fileName has been already inserted \n");
                    continue;
                }

                $rows = Common::readFromCsv($filePath);
                unset($rows[0]);

                //Fetch product
                $productId = current(explode("-", $fileName));

                $product = Product::fetch($productId);
                if (!$product) {
                    $newProduct['product_id'] = $productId;
                    $newProduct['title'] = '';
                    $newProduct['category_id'] = $file['categoryId'];
                    $product = Product::insert($newProduct);
                }

                $commentData['product_id'] = $product->id;
                foreach ($rows as $key => $row) {
                    $commentData['positive_points'] = $row[0];
                    $commentData['negative_points'] = $row[1];
                    $commentData['text'] = $row[2];
                    $comment = Comment::updateOrInsert($commentData);
                    $whereRaw = "entity_id = $comment->id AND entity_type_id = $commentType->id";
                    Rating::deleteRatings($whereRaw);

                    print_r("Comment $comment->id  of product $product->id in line $key of file $fileName has been inserted/updated \n");

                    /**Saving ratings related to the comment**/
                    $ratingData['entity_id'] = $comment->id;
                    $ratingData['entity_type_id'] = $commentType->id;

                    $ratingData['rating_type_id'] = $likeType->id;
                    $updateData['rate'] = intval($row[3]);
                    Rating::updateOrInsert($ratingData, $updateData);

                    $ratingData['rating_type_id'] = $dislikeType->id;
                    $updateData['rate'] = intval($row[4]);
                    Rating::updateOrInsert($ratingData, $updateData);

                    $ratingData['rating_type_id'] = $aspectsType->id;
                    $rates = [];
                    $i = 5;
                    foreach ($aspects as $aspect) {
                        if (isset($row[$i])) {
                            $rates[$aspect->id] = intval($row[$i]);
                        } else {
                            $rates[$aspect->id] = 0;
                        }
                        $i++;
                    }

                    $updateData['rate'] = serialize($rates);
                    $rating = Rating::updateOrInsert($ratingData, $updateData);

                    print_r("Rating for Comment $comment->id  of product $product->id in line $key of file $fileName has been inserted/updated \n");
                }

                //Save file name in log table to avoid reading again
                $fileLooData['file_name'] = $fileName;
                $fileLog = FileLog::insert($fileLooData);
                print_r("File log " . $fileLog->id . " has been inserted/updated \n");
            }
        }
    }

    /**
     * @param $word2VecDir
     */
    public static function generateWord2VecInput($word2VecDir)
    {
        $categories = Category::fetchCategories();
        foreach ($categories as $category) {
            $word2vecInputText = '';

            foreach ($category->products as $product) {
                $word2vecInputText .= $product->description . "\n";
            }

            foreach ($category->comments as $comment) {
                print_r("id: $comment->id \n");
                $word2vecInputText .= $comment->text . "\n" . $comment->positive_points . "\n" . $comment->negative_points . "\n";
                foreach (Tokenizer::$wordDelimiters as $wordDelimiter) {
                    $word2vecInputText = str_replace($wordDelimiter, " ", $word2vecInputText);
                }
            }
            if (!$word2vecInputText) {
                continue;
            }
            $fileDir = $word2VecDir. $category->title;
            Common::makeDirectory($fileDir);
            $filePath = $fileDir . "/corpus.txt";
            $file = fopen($filePath, 'w+');
            fwrite($file, $word2vecInputText);

            print_r("\n Input for category ". $category->id ." has been generated! \n");

            $word2vecPath = $fileDir."/vec.txt";
            echo exec("python -m gensim.scripts.word2vec_standalone -train ".$filePath." -output ".$word2vecPath." -size 200 -sample 1e-4 -binary 0 -iter 3");
        }
    }
}