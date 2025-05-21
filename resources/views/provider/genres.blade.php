@extends('layouts.app')


<div class="w-full max-h-full flex flex-wrap pt-[35px] gap-x-0">
  @foreach($genres as $g)
    <div
      class="w-[50%] h-max pr-[40px] pb-[10px]"
    >
      <button 
        onclick="document.location.href='{{ route('provider.generateSession', ['providerId' => $providerId,'genreId' => $g['id']]) }}'" 
        class="
          bg-[#10141e]
          w-full
          h-[50px]
          text-xl
          rounded-4xl 
          flex 
          items-center 
          justify-center 
          cursor-pointer
          text-white
          border-1
          border-transparent
          hover:bg-[#1a2031]
          hover:border-[#252835]
          hover:scale-[1.02] 
          duration-200 
        "
      >
        {{ $g['name'] }}
      </button>
    </div>

  @endforeach
</div>
