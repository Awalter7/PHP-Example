<!-- resources/views/session/join.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="w-screen h-screen flex items-center justify-center gap-[10px]">
    <div class="flex flex-col gap-[10px]">


      <div class="flex gap-[10px] items-end">
        <div class="flex flex-col gap-[10px]">
          <input 
            id="name-input"
            placeholder="Name"
            class="w-[300px] h-[50px] bg-[#10141e] border border-[#252835] rounded-3xl px-5"
          />
          <input 
            id="session-code-input"
            placeholder="Session Code"
            class="w-full h-[50px] bg-[#10141e] border border-[#252835] rounded-3xl px-5"
          />
        </div>

        <button 
          id="join-session-btn"
          class="px-6 text-lg rounded-3xl bg-[#1a2031] h-[50px] flex items-center border border-[#252835] cursor-pointer"
        >
          Join Session
        </button>
      </div>
      <button 
          id="join-session-btn"
          onclick="document.location.href='{{ route('provider.index') }}'" 
          class="px-6 text-lg rounded-3xl bg-[#1a2031] h-[50px] flex items-center border border-[#252835] cursor-pointer mt-[25px]"
        >
          Create Session
      </button>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.getElementById('join-session-btn').addEventListener('click', async () => {
      const code = document.getElementById('session-code-input').value.trim();
      const name = document.getElementById('name-input').value.trim();
      if (!code || !name) return alert('Please enter both a session code and your name.');

      const res = await fetch(`${window.location.origin}/session/${code}/join`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name }),
      });

      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        return alert(err.error || 'Failed to join session.');
      }

      // redirect into the session room
      window.location.href = `/session/${code}`;
    });
  </script>
@endpush
