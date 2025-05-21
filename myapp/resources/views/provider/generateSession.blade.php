<div class="w-full max-h-full flex flex-col gap-[10px] pt-[35px]">
  <div class="w-full flex gap-[10px]">
    <div id="session-code" class="w-full h-[50px] bg-[#10141e] border-1 border-[#252835] rounded-3xl flex px-[20px] items-center">
      <!-- generated code goes here -->
    </div>
    <button 
      id="gen-code-btn"
      class="
        w-max 
        h-[50px] 
        bg-[#1a2031] 
        rounded-3xl 
        flex 
        items-center 
        text-lg 
        px-[20px] 
        cursor-pointer
        border-1
        border-[#252835]
        duration-200 
      "
    >
      Generate
    </button>
  </div>
  <div class="w-full flex gap-[10px]">
    <input 
      id="name-input" 
      placeholder="name" 
      class="w-full h-[50px] bg-[#10141e] border-1 border-[#252835] rounded-3xl flex px-[20px] items-center"
/>
    <button 
      id="join-session-btn"
      class="
        w-max 
        h-[50px] 
        bg-[#1a2031] 
        rounded-3xl 
        flex 
        items-center 
        text-lg 
        px-[20px] 
        cursor-pointer
        border-1
        border-[#252835]
        text-white
        duration-200 
      "
    >
      Join
    </button>
  </div>
</div>

@push('scripts')
<script>
  // 1) Generate code and display it
  document.getElementById('gen-code-btn')
    .addEventListener('click', async () => {
      const url = `{{ route('provider.sessionCode', ['providerId' => $providerId, 'genreId' => $genreId]) }}`;
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept':       'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
      });
      if (!res.ok) return alert('Failed to generate code');
      const { code } = await res.json();
      document.getElementById('session-code').textContent = code;
    });

  // 2) Redirect when the "Join Session" button is clicked
  document.getElementById('join-session-btn')
    .addEventListener('click', async () => {                    // ← add async here
      const code = document.getElementById('session-code').textContent.trim();
      const name = document.getElementById('name-input').value.trim();
      if (!code) return alert('Please generate a session code first.');
      if (!name) return alert('Please enter a name first.');

      const res = await fetch(`/session/${code}/join`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept':       'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name })
      });

      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        return alert(err.error || 'Failed to join session.');
      }

      window.location.href = `/session/${code}`;
    });
</script>
@endpush
