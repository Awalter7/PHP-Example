
<!-- resources/views/netflix/movies.blade.php -->
@extends('layouts.app')

@section('content')
  <div 
    class="w-screen h-screen flex flex-col items-center justify-center px-8 pt-8 gap-8 bg-no-repeat"
    style=""
  >
    <div class="fixed top-0 left-0 w-screen h-max p-[15px] flex justify-between gap-[10px]">
      <div id='participants-list'>

      </div>
      <div class="flex flex-col gap-[10px] bg-[#1f1f1f] p-[5px] rounded-lg">
        <div id='my-likes' class="bg-green-500/50 px-[10px] py-[5px] rounded-4xl">

        </div>
        <div id='my-dislikes' class="bg-red-500/50 px-[10px] py-[5px] rounded-4xl">

        </div>
      </div>

    </div>
    <div class="flex flex-col items-center w-full h-full justify-between bg-no-repeat bg-top bg-contain rounded-md" >
        <div id="movie-slider" class="z-1 min-h-full w-full" data-currentName="{{$currentName}}" data-movies='@json($movies)' data-code="{{$code}}" data-sessionid="{{ session()->getId() }}"></div>
    </div>
    <h2 id="movieTitle" class="text-white text-xl mt-4"></h2>
  </div>
@endsection

@push('scripts')
  @vite('resources/js/app.jsx')
  <script>
    const code = @json($code);
    const container = document.getElementById('participants-list');
    const myLikes = document.getElementById('my-likes')
    const myDislikes = document.getElementById('my-dislikes')


    async function refreshParticipants() {
      try {
        const res = await fetch(`/session/${code}/participants`);

        if (!res.ok) throw new Error('Network error');

        const parts = await res.json();

        container.innerHTML = parts.map(p => {
          if(@json($currentName) !== p.name){
            return(
              `<div class="participant bg-[#1f1f1f] w-max pl-[10px] pr-[5px] py-[5px] flex gap-[5px] rounded-lg">
                <span class="mr-[10px]">${p.name}</span>
                
                <div class="bg-green-500 px-[10px] rounded-4xl">${p.approved.length}</div> 
                <div class="bg-red-500 px-[10px] rounded-4xl">${p.disapproved.length}</div> 
              </div>`
            )
          }else{
            myLikes.innerHTML = p.approved.length;
            console.log(p.disapproved)
            myDislikes.innerHTML = p.disapproved.length;
          }

        }).join('');
      } catch (e) {
        console.error('Failed to refresh participants:', e);
      }
    }


    refreshParticipants();
    setInterval(refreshParticipants, 1000);
  </script>
@endpush