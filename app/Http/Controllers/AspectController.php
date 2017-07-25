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
        $whereClause = "1";
        if ($request->categoryId) {
            $categoryId = $request->categoryId;
            $whereClause = "category_id IN (". $categoryId;
            $descendants = Category::fetchDescendants($categoryId);
            foreach ($descendants as $descendant) {
                $whereClause .= ", $descendant->id";
            }
            $whereClause .= ")";
        }

        $aspects = Aspect::fetchAspects("*", $whereClause);
        foreach ($aspects as &$aspect) {
            $category = $aspect->category;
            $aspect->category = $category;
            $aspect->keywords = unserialize($aspect->keywords);
        }
        unset($aspect);

        return Datatables::of($aspects)->make(true);
    }}
