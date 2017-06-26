<?php

namespace App\Http\Controllers;

use App\Helpers\Common;
use App\Libraries\AspectLib;
use App\Models\Category;
use App\Models\Comment;
use App\Models\FileLog;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;


class CommentController extends Controller
{


    public function viewList($productId)
    {
        if ($productId) {
            $product = Product::fetch($productId);
            return view('comment.list', compact('product'));
        }
    }

    public function getList(Request $request)
    {
        $this->validate($request, [
            'productId' => 'required',
        ]);

        $product = Product::fetch($request->productId);
        $comments = $product->comments;
        $category = $product->category;
        $aspects = $category->aspects;
        foreach ($comments as $comment) {
            $comment->aspects = $aspects;
        }
        return Datatables::of($comments)->make(true);

    }

    public function updateComments(Request $request)
    {
        $result = [];
        $this->validate($request, [
            'comments' => 'required'
        ]);

        $commentsArray = $request->comments;

        foreach ($commentsArray as $commentData) {
            $comment = Comment::fetch($commentData[0]);
            $comment->gold_selected = $commentData[1];
            $comment->sentiment_polarity = $commentData[2];
            $comment->save();
        }
        $result['message'] = 'Comments have been successfully updated';

        return $result;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('comment.upload-panel');
    }

    public function upload(Input $input)
    {
        //set_time_limit(0);
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
            $aspectIds = AspectLib::retrieveAspectIds(0,'Mobile-Phone');
            $commentType = Type::fetch('comment');
            $likeType = Type::fetch('like');
            $dislikeType = Type::fetch('dislike');
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

                    //Saving Product
                    $productId = current(explode("-", $fileName));
                    $product = Product::fetch($productId);
                    if (!$product) {
                        $newProduct['product_id'] = $productId;
                        $newProduct['title'] = '';
                        $newProduct['category_id'] = 4;
                        $product = Product::insert($newProduct);
                    } else {
                        Comment::deleteComments('product_id = ' . $productId);
                    }

                    if (($handle = fopen($file->getPathName(), "r")) !== false) {
                        $titlesRow = fgetcsv($handle); //get header row
                        while (($row = fgetcsv($handle)) !== false) {
                            //Saving Comment
                            $newComment = [];
                            $newComment['product_id'] = $product->id;
                            $newComment['positive_points'] = $row[0];
                            $newComment['negative_points'] = $row[1];
                            $newComment['text'] = $row[2];
                            $newComment['is_analysed'] = 0;
                            $comment = Comment::insert($newComment);

                            /**Saving ratings related to the comment**/
                            $rating = new Rating();
                            $newRating['entity_id'] = $comment->id;
                            $newRating['entity_type_id'] = $commentType->id;

                            $newRating['rating_type_id'] = $likeType->id;
                            $newRating['rate'] = intval($row[3]);
                            Rating::insert($newRating);

                            $newRating['rating_type_id'] = $dislikeType->id;
                            $newRating['rate'] = intval($row[4]);
                            Rating::insert($newRating);

                            /*Store aspects*/
                            $newRating['rating_type_id'] = $aspectsType->id;
                            $aspectIndex = 5;
                            foreach ($aspectIds as $aspectId) {
                                $rates[$aspectId] = intval($row[$aspectIndex]);
                                $aspectIndex++;
                            }
                            $newRating['rate'] = serialize($rates);
                            Rating::insert($newRating);
                        }
                        fclose($handle);
                    }

                    //Save comment name in log table to avoid reading again
                    FileLog::insert($fileName);
                    $results['success'][] = [
                        1,
                        'File ' . $fileName . ' is successfully inserted'
                    ];

                } else {
                    $results['errors'] = $extensionValidator->errors()->all();
                }
            }
        }

        return redirect('upload-panel')->with('results', $results);
    }




}
