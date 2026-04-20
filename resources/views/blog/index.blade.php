@extends('layouts.theme')
@section('title', 'Printbuka BLog')
@section('content')
    <main role="main">
        {{-- Title --}}
        <div class="title text-center flex flex-col space-y-2 justify-center items-center min-h-50 bg-gray-950">
            <h1 class="text-center text-5xl text-gray-200">Printbuka Blog</h1>
            <p class="text-gray-200 text-center">Catch the lastest tips, News</p>
        </div>

        {{-- B;log content Grid --}}
        <div class="grid grid-cols-2 w-[90%] self-center place-content-center justify-center p-5 mt-5 gap-8 ">
            <div id="posts" class="w-full">
                @forelse ($showPosts as $posts)
                    <div class="post">
                        <h1 class="post-header">{{ $post-> title }}</h1>
                        <p class="excderpt">{{ $post-> excerpt }}</p>
                        <p class="text-sm text-gray-300">{{ $post->published_at }}</p>
                        <hr>
                        <a href="#"><button>Read More .....</button></a>
                    </div>
                @empty
                    <div class="p-3 bg-gray-200 text-gray-300 rounded-xl border-2 border-gray-700 border-dotted flex flex-col items-center justify-center">
                        <p class="text-4xl text-gray-400 text-center">There are no POsts at this time. Please check back later</p>
                    </div>
                @endforelse
            </div>

            <div id="widgets">
                <div class="widget1 p-3 space-y-3">
                    <h1 class="widget-header text-3xl font-bold">Tutorials</h1>
                </div>
                <hr>
                <div class="widget1 p-3 space-y-3">
                    <h1 class="widget-header text-3xl font-bold">Ads</h1>
                </div>
            </div>
        </div>
    </main>
@endsection