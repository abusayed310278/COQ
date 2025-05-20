<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleReviewController extends Controller
{
    public function fetch()
    {
        $placeId = 'ChIJY69GG3yKdkgRdOxtgXd91fc'; // Replace with actual Google Place ID
        $apiKey = config('services.google_places.key');

        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $placeId,
            'fields' => 'rating,reviews,user_ratings_total',
            'key' => $apiKey,
        ]);

        return $response->json();
    }
}
