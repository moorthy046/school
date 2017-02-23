<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Page $page)
    {
        if(!isset($page->id))
        {
            $page = Page::first();
            if(!isset($page->id))
            {
                return redirect('signin');
            }
        }
        $title = $page->title;
        $pages = Page::select('title','id')->get();
        if($pages->count()==0)
        {
            return redirect('signin');
        }
        return view('page', compact('page','title','pages'));
    }
}
