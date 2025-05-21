<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Services\Provider;

class SessionShareController extends Controller
{
    // Create a share code
    protected Provider $provider;
    
    public function __construct(Provider $provider)
    {
        // Laravel will auto-resolve this for you
        $this->provider = $provider;
    }

    public function create(Request $request, $providerId, $genreId)
    {
        $request->session()->put([

        ]);


        $sid  = $request->session()->getId();
        $code = Str::upper(Str::random(6));      // e.g. X7G4KZ

        $payload = [
            'session_id' => $sid,
            'providerId' => $providerId,
            'genreId'    => $genreId,
            'expires_at' => now()->addHour()->toDateTimeString(),
        ];

        Storage::put("session_shares/{$code}.json", json_encode($payload));

        return response()->json(['code' => $code]);
    }

    // Join by code
    public function join(Request $request, string $code)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $path = "session_shares/{$code}.json";

        if (! Storage::exists($path)) {
            abort(404, 'Invalid session code');
        }

        $data = json_decode(Storage::get($path), true);
        $sessionId = $request->session()->getId();
        $name = $request->input('name');
        
        // append this user to participants
        $data['participants'] = $data['participants'] ?? [];
        $data['participants'][] = [
            'name'      => $name ,
            'joined_at' => now()->toDateTimeString(),
            'session_id'  => $sessionId,
            'approved'    => [],              // ← initialize an empty array
            'disapproved' => [],
        ];

        $request->session()->put('participant_name:' . $code, $name);

        // check expiration
        if (now()->gt(\Carbon\Carbon::parse($data['expires_at']))) {
            Storage::delete($path);
            abort(410, 'Code expired');
        }

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        // optional: store code in their session
        $request->session()->put('joined_code', $code);

        return response()->json([
            'message' => 'Joined successfully',
            'name'    => $name,
        ]);
    }

    public function showSession(Request $request, string $code)
    {
        $path = "session_shares/{$code}.json";

        if (! Storage::exists($path)) {
            abort(404, 'Session not found');
        }

        $payload = json_decode(Storage::get($path), true);

        // 1) check expiry
        if (Carbon::now()->greaterThan(Carbon::parse($payload['expires_at']))) {
            abort(410, 'Session has expired');
        }

        // 2) pull out your IDs
        $providerId = $payload['providerId'];
        $genreId    = $payload['genreId'];
        $participants = $payload['participants'] ?? [];
        $movies = $this->provider->getProviderMoviesByGenre($providerId, $genreId);
        $currentName = $request->session()->get('participant_name:' . $code, 'Guest');


        // now you can pass them to a blade
        return view('session.session', compact('code','providerId','genreId', 'currentName', 'movies', 'participants'));
    }

    public function updateApproval(Request $request, string $code)
    {
        $request->validate([
            'movie_id' => 'required|integer',
            'approve'  => 'sometimes|boolean',
        ]);

        $path = "session_shares/{$code}.json";
        if (! Storage::exists($path)) {
            return response()->json(['error' => 'Invalid session'], 404);
        }

        $data      = json_decode(Storage::get($path), true);
        $sessionId = $request->session()->getId();
        $movieId   = $request->input('movie_id');
        $approve   = $request->boolean('approve', true);
        $found     = false;

        foreach ($data['participants'] as & $p) {
            if (($p['session_id'] ?? null) === $sessionId) {
                // make sure both arrays exist
                $p['approved']   = $p['approved']   ?? [];
                $p['disapproved'] = $p['disapproved'] ?? [];

                if ($approve) {
                    // ➕ Add to approved, remove from disapproved if present
                    if (! in_array($movieId, $p['approved'])) {
                        $p['approved'][] = $movieId;
                    }
                    $p['disapproved'] = array_filter(
                        $p['disapproved'],
                        fn($id) => $id !== $movieId
                    );
                } else {
                    // ➖ Remove from approved, add to disapproved
                    $p['approved'] = array_filter(
                        $p['approved'],
                        fn($id) => $id !== $movieId
                    );
                    if (! in_array($movieId, $p['disapproved'])) {
                        $p['disapproved'][] = $movieId;
                    }
                }

                $found = true;
                break;
            }
        }

        if (! $found) {
            return response()->json(
                ['error' => 'You must join before updating approval'],
                403
            );
        }

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json([
            'approved'   => $p['approved'],
            'disapproved'=> $p['disapproved'],
        ]);
    }

    public function participantsJson(string $code)
    {
        $path = "session_shares/{$code}.json";

        if (! Storage::exists($path)) {
            return response()->json([], 404);
        }

        $data = json_decode(Storage::get($path), true);

        $payload = collect($data['participants'] ?? [])
            ->map(function($p) {
                return $p;
            })
            ->values();  // reset numeric keys

        return response()->json($payload);
    }
}
