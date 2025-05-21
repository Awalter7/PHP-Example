


  <div class="w-full h-full bg-black flex flex-col items-center">
    <div class="w-full h-0 flex items-start pointer-events-none">
        <div class="w-full h-[50px] z-1" style="background: linear-gradient(180deg,rgba(3, 7, 18, 1) 20%, rgba(0, 0, 0, 0) 100%);">

        </div>
    </div>  
    <div class="flex flex-wrap items-center justify-center w-full h-full z-0 overflow-y-scroll py-[35px] pr-[10px]">
        @foreach ($providers as $p)
            <button 
                class="
                    
                    w-[25%]
                    pb-[10px]
                    pr-[10px]
                    aspect-[1/1] 
                    flex 
                    items-center 
                    justify-center
                    cursor-pointer
                "
                onclick="document.location.href='{{ route('provider.genres',['providerId' => $p['provider_id']]) }}'"
            >
                <img 
                    src="https://image.tmdb.org/t/p/w500{{$p['logo_path']}}" 
                    alt="Hulu logo" 
                    width="100" 
                    height="100"
                    class="
                        bg-white/10 
                        border-1 
                        border-[#252835]
                        shadow-md 
                        shadow-[#252835]/20 
                        hover:shadow-[#252835] 
                        hover:shadow-lg 
                        hover:scale-[1.05]
                        rounded-2xl 
                        duration-200
                        w-full
                        h-full
                    "
                >
            </button>
        @endforeach
    </div>
    <div class="w-full h-0 flex items-end pointer-events-none">
        <div class="w-full h-[50px] z-1" style="background: linear-gradient(0deg,rgba(3, 7, 18, 1) 20%, rgba(0, 0, 0, 0) 100%);">

        </div>
    </div>  
  </div>
