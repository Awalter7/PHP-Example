<?php
namespace App\Services;

use GuzzleHttp\Client;

class Provider
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.themoviedb.org/3/']);
    }

    public function getProviders(): array
    {
        $apiKey = config('services.tmdb.key');

        // TMDB’s “watch providers” endpoint for movies
        $response = $this->client->get('watch/providers/movie', [
            'query' => [
                'api_key'      => $apiKey,
                'watch_region' => 'US',
            ],
            'verify' => false,     // ← dev only!
        ]);

        // decode the JSON response
        $payload = json_decode($response->getBody()->getContents(), true);

        // return the providers array (or an empty array if missing)
        return $payload['results'] ?? [];
    }


    public function getProviderGenres(int $providerId): array
    {
        $apiKey = config('services.tmdb.key');

        // 1) Discover provider movies (grab first page for brevity)
        $resp = $this->client->get('discover/movie', [
            'query' => [
                'api_key'                     => $apiKey,
                'language'                    => 'en-US',
                'with_watch_providers'        => $providerId,
                'watch_region'                => 'US',
                'with_watch_monetization_types'=> 'flatrate',
                'page'                        => 1,
            ],
            'verify' => false,     // ← dev only!
        ]);
        $movies = json_decode($resp->getBody(), true)['results'];

        // 2) Collect unique genre IDs
        $genreIds = [];
        foreach ($movies as $m) {
            foreach ($m['genre_ids'] as $gid) {
                $genreIds[$gid] = true;
            }
        }

        // 3) Fetch all TMDB genres
        $resp2   = $this->client->get('genre/movie/list', [
            'query' => ['api_key' => $apiKey, 'language' => 'en-US'],
            'verify' => false,     // ← dev only!
        ]);
        $all     = json_decode($resp2->getBody(), true)['genres'];

        // 4) Filter
        return array_values(array_filter(
            $all,
            fn($g) => isset($genreIds[$g['id']])
        ));
    }

    public function getProviderMoviesByGenre(int $providerId, int $genreId): array
    {
        $apiKey   = config('services.tmdb.key');
        $allMovies = [];
        $page      = 1;
        $totalPages = 1;

        do {
            // 1) Request discover/movie?page=$page
            $resp = $this->client->get('discover/movie', [
                'query' => [
                    'api_key'                       => $apiKey,
                    'language'                      => 'en-US',
                    'with_watch_providers'          => $providerId,
                    'watch_region'                  => 'US',
                    'with_watch_monetization_types' => 'flatrate',
                    'with_genres'                   => $genreId,
                    'page'                          => $page,
                ],
                'verify' => false,
            ]);

            // 2) Decode the full payload (not just results)
            $payload = json_decode($resp->getBody()->getContents(), true);

            // 3) Merge this page’s results
            $allMovies = array_merge($allMovies, $payload['results']);

            // 4) Figure out how many pages there are
            $totalPages = $payload['total_pages'];          // TMDb caps at 1000 pages :contentReference[oaicite:2]{index=2}
            $page++;
        } while ($page <= $totalPages);

        return $allMovies;
    }
}
