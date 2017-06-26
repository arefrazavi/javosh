<?php

namespace App\Http\Controllers;

use App\Models\Aspect;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class AspectController extends Controller
{
    public function viewList($categoryId = 0)
    {
        return view('aspect.list', compact('categoryId'));
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

        $aspects = Aspect::fetchAspects("*", $whereRaw);
        foreach ($aspects as &$aspect) {
            $category = $aspect->category;
            $aspect->category = $category;
            $aspect->keywords = unserialize($aspect->keywords);
        }
        unset($aspect);

        return Datatables::of($aspects)->make(true);
    }}
