<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = $request->input('radius');

        $client = new Client();
        $response = $client->get('https://overpass-api.de/api/interpreter', [
            'query' => [
                'data' => '[out:json];node(around:' . $radius . ',' . $latitude . ',' . $longitude . ')[amenity=pub];out;',
            ],
        ]);

        $restaurants = json_decode($response->getBody()->getContents(), true);
        $formattedRestaurants = [];
        
        foreach ($restaurants['elements'] as $restaurant) {
            $formattedRestaurants[] = [
                'city' => $restaurant['tags']['addr:city'] ?? null,
                'street' => $restaurant['tags']['addr:street'] ?? null,
                'latitude' => $restaurant['lat'],
                'longitude' => $restaurant['lon'],
                'name' => $restaurant['tags']['name'] ?? null,
            ];
        }
        
        return response()->json($formattedRestaurants);
    }
}