
<!-- resources/views/netflix/movies.blade.php -->
@extends('layouts.app')

@section('content')
  <div 
    class="w-screen h-screen flex items-center justify-center px-25 py-25 gap-[40px] bg-no-repeat"
  >
    <div class="w-full h-full ">
      <div class="h-[50px]">
        <h1 class="text-3xl text-white">
          Services
        </h1>
      </div>
      <div class="flex flex-col w-full h-[calc(100%_-_50px)] border-r-1 border-[#252835]  pt-[25px]">
        @include('provider.providers')  
      </div>
    </div>
    <div class="w-full h-full ">
      @isset($genres)
        <div class="h-[50px]">
          <h1 class="text-3xl text-white">
            Genres
          </h1>
        </div>
        <div class="flex flex-col w-full h-[calc(100%_-_50px)] border-r-1 border-[#252835] pr-[10px] pt-[25px]">
          @include('provider.genres')  
        </div>
      @endisset
    </div>
    <div class="w-full h-full">
      @isset($genreId)
        <div class="h-[50px]">
          <h1 class="text-3xl text-white">
            Generate Session
          </h1>
        </div>
        <div class="flex flex-col w-full h-[calc(100%_-_50px)] border-r-1 border-[#252835] pr-[10px] pt-[25px]">
           @include('provider.generateSession') 
        </div>
      @endisset
    </div>
  </div>
@endsection

@push('scripts')
  @vite('resources/js/app.jsx')
@endpush