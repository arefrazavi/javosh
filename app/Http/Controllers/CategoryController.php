<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class CategoryController extends Controller
{
    public function viewList()
    {
        return view('category.list');
    }

    public function getList()
    {
        $selectRaw = "categories.*, c2.alias as parent_alias";
        $categories = Category::fetchCategories($selectRaw, "1", PHP_INT_MAX, 0, "id", "DSC");

        $newCategories = [];

        foreach ($categories as $category) {
            $newCategories[$category->id] = $category;
            $newCategories[$category->id]->productsCount = 0;
            $newCategories[$category->id]->commentsCount = 0;
        }

        foreach ($categories as $category) {
            $products = $category->products;
            $newCategories[$category->id]->productsCount += $products->count();
            foreach ($products as $product) {
                $newCategories[$category->id]->commentsCount += Comment::fetchComments("COUNT(*) AS count",
                    PHP_INT_MAX, 0, "product_id = " . $product->id)[0]->count;
            }

            if (isset($newCategories[$category->parent_id])) {
                $newCategories[$category->parent_id]->productsCount += $category->productsCount;
                $newCategories[$category->parent_id]->commentsCount += $category->commentsCount;
            }
        }

        return Datatables::of($categories)->make(true);

    }
}
