<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $posts = Post::where('title','LIKE',"%$query%")->approved()->published()->get();
       return view('search_posts',compact('posts','query'));
    }
}
