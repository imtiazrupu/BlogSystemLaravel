<?php

namespace App\Http\Controllers\Author;

use App\Category;
use App\Tag;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAuthorPost;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Auth::User()->posts()->latest()->get();
        return view('author.post.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('author.post.create', compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title' => 'required',
            'image' => 'required|mimes:jpg,bmp,png,jpeg',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required',

        ]);

          // get form image
          $image = $request->file('image');
          $slug = str_slug($request->title);
          if(isset($image))
          {
              // make unique name for image
              $currentDate = Carbon::now()->toDateString();
              $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
              // check post directory is exists
              if(!Storage::disk('public')->exists('post'))
              {
                  Storage::disk('public')->makeDirectory('post');
              }
              // resize image for post and upload
              $postImage = Image::make($image)->resize(1600,1066)->stream();
              Storage::disk('public')->put('post/'.$imageName,$postImage);
            }else{
              $imageName = "default.png";
          }
          $post = new Post();
          $post->user_id = Auth::id();
          $post->title = $request->title;
          $post->slug = $slug;
          $post->image = $imageName;
          $post->body = $request->body;
          if(isset($request->status))
          {
              $post->status = true;
          }else{
              $post->status = false;
          }
          $post->is_approved =false;
          $post->save();
          $post->categories()->attach($request->categories);
          $post->tags()->attach($request->tags);

          $users = User::where('role_id','1')->get();
          Notification::send($users, new NewAuthorPost($post));
          
          Toastr::success('Post Created Successfully', 'Success');
          return redirect()->route('author.post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        if($post->user_id != Auth::id())
        {
            Toastr::error('You are not authorized to access this post', 'Error');
            return redirect()->route('author.post.index');
        }
        return view('author.post.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if($post->user_id != Auth::id())
        {
            Toastr::error('You are not authorized to access this post', 'Error');
            return redirect()->route('author.post.index');
        }
        $categories = Category::all();
        $tags = Tag::all();

        return view('author.post.edit', compact('categories','tags','post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        if($post->user_id != Auth::id())
        {
            Toastr::error('You are not authorized to access this post', 'Error');
            return redirect()->route('author.post.index');
        }
        $this->validate($request,[
            'title' => 'required',
            'image' => 'image',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required',

        ]);

          // get form image
          $image = $request->file('image');
          $slug = str_slug($request->title);
          if(isset($image))
          {
              // make unique name for image
              $currentDate = Carbon::now()->toDateString();
              $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
              // check post directory is exists
              if(!Storage::disk('public')->exists('post'))
              {
                  Storage::disk('public')->makeDirectory('post');
              }
              //delete old image
              if(Storage::disk('public')->exists('post/'.$post->image))
              {
                  Storage::disk('public')->delete('post/'.$post->image);
              }
              // resize image for post and upload
              $postImage = Image::make($image)->resize(1600,1066)->stream();
              Storage::disk('public')->put('post/'.$imageName,$postImage);
            }else{
              $imageName = $post->image;
          }
          $post->user_id = Auth::id();
          $post->title = $request->title;
          $post->slug = $slug;
          $post->image = $imageName;
          $post->body = $request->body;
          if(isset($request->status))
          {
              $post->status = true;
          }else{
              $post->status = false;
          }
          $post->is_approved =false;
          $post->save();
          $post->categories()->sync($request->categories);
          $post->tags()->sync($request->tags);
          Toastr::success('Post Updated Successfully', 'Success');
        return redirect()->route('author.post.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if($post->user_id != Auth::id())
        {
            Toastr::error('You are not authorized to access this post', 'Error');
            return redirect()->route('author.post.index');
        }
        if(Storage::disk('public')->exists('post/'.$post->image))
            {
                storage::disk('public')->delete('post/'.$post->image);
            }
            $post->categories()->detach();
            $post->tags()->detach();
            $post->delete();
            Toastr::success('Post Delete Successfully', 'Success');
            return redirect()->back();
    }
}
