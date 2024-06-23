<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\Message;
use App\Models\Service;
use GrahamCampbell\ResultType\Success;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HousesController extends Controller
{
    public function index()
    {
        $houses = House::with('user', 'messages', 'images', 'services', 'sponsors')->get();
        return response()->json($houses);
    }

    public function getNearbyCastles(Request $request)
    {
        // Recupero i parametri dalla richiesta
        $address = $request->input('address');
        $radius = $request->input('range', 100);
        $rooms = $request->input('rooms');
        $beds = $request->input('beds');
        $bathrooms = $request->input('bathrooms');
        $services = $request->input('services');

        // Inizializzo la query base
        $query = House::with('user', 'messages', 'images', 'services', 'sponsors');

        if (!empty($address)) {
            // Chiamata all'API di TomTom per ottenere le coordinate geografiche
            $cacertPath = env('CACERT_PEM_PATH');
            $client = new Client([
                'base_uri' => 'https://api.tomtom.com/',
                'verify' => $cacertPath,
            ]);

            $response = $client->get('search/2/geocode/' . $address . '.json', [
                'query' => ['key' => env('TOMTOM_API_KEY')]
            ]);

            $data = json_decode($response->getBody(), true);

            if (empty($data['results'])) {
                return response()->json(['error' => 'Indirizzo non trovato'], 404);
            }

            $position = $data['results'][0]['position'];
            $latitude = $position['lat'];
            $longitude = $position['lon'];

            // Cerco i castelli entro il range specificato
            $query->selectRaw(
                "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
                ->having('distance', '<', $radius)
                ->orderBy('distance');
        } else {
            // Ordina per data di creazione se non è specificata la distanza
            $query->orderBy('created_at', 'desc');
        }

        // Filtro per numero di stanze
        if ($rooms) {
            $query->where('rooms', '>=', $rooms);
        }

        // Filtro per numero di posti letto
        if ($beds) {
            $query->where('bed', '>=', $beds);
        }

        // Filtro per numero di bagni
        if ($bathrooms) {
            $query->where('bathrooms', '>=', $bathrooms);
        }

        // Filtro per servizi
        if ($services && is_array($services)) {
            foreach ($services as $serviceId) {
                $query->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('id', $serviceId);
                });
            }
        }

        // Clono la query base per i castelli sponsorizzati e non sponsorizzati
        $sponsoredQuery = clone $query;
        $nonSponsoredQuery = clone $query;

        // Query per case sponsorizzate
        $sponsoredHouses = $sponsoredQuery->whereHas('sponsors')->get();

        // Query per case non sponsorizzate
        $nonSponsoredHouses = $nonSponsoredQuery->whereDoesntHave('sponsors')->get();

        // Unisco i risultati, con le case sponsorizzate per prime
        $houses = $sponsoredHouses->merge($nonSponsoredHouses);

        return response()->json($houses);
    }

    public function getServices()
    {
        $services = Service::orderBy('name')->get();
        return response()->json($services);
    }

    public function getHouseBySlug($slug)
    {
        $house = House::where('slug', $slug)->with('user', 'messages', 'images', 'services', 'sponsors')->first();

        return response()->json($house);
    }

    public function getHousesByServices($slug)
    {
        $service = Service::where('slug', $slug)->with('houses')->first();
        return response()->json($service);
    }
}
