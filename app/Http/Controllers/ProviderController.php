<?php
namespace App\Http\Controllers;

use App\Services\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        // Laravel will auto-resolve this for you
        $this->provider = $provider;
    }

    public function showProviders()
    {
        
        $providers = $this->provider->getProviders();
        return view('provider.layout', compact('providers'));
    }

    public function showGenres(int $providerId)
    {
        $providers = $this->provider->getProviders();
        $genres = $this->provider->getProviderGenres($providerId);
        return view('provider.layout', compact('providers','genres','providerId'));
    }

    public function showGenerateSession(int $providerId, int $genreId)
    {
        $providers = $this->provider->getProviders();
        $genres = $this->provider->getProviderGenres($providerId);
        return view('provider.layout', compact('providers','genres', 'genreId','providerId'));
    }

    public function movies($genre, int $providerId)
    {
        $movies = $this->provider->getProviderMoviesByGenre((int)$genre, $providerId);
        return view('provider.movies', compact('movies'));
    }
}