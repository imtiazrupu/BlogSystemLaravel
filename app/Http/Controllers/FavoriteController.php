<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class FavoriteController extends Controller
{
    public function add($id)
    {
        $user = Auth::user();
        $isFavorite = $user->favorite_posts()->where('post_id',$id)->count();

        if($isFavorite == 0)
        {
            $user->favorite_posts()->attach($id);
            Toastr::success('Post Successfully Added To Your Favorite List', 'Success');
            return redirect()->back();
        }else{
            $user->favorite_posts()->detach($id);
            Toastr::success('Post Successfully Remove To Your Favorite List', 'Success');
          return redirect()->back();
        }
    }
}
