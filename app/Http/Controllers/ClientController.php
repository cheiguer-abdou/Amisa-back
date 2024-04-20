<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1); // Default to page 1 if not provided
        $size = $request->input('size', 5); // Default to 5 rows per page if not provided

        $clients = Client::orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $page);

        // Add image URLs to each client
        $clients->getCollection()->transform(function ($client) {
            if ($client->image) {
                // Assuming image path is already correct, no need to modify it
                $client->image_url = asset("storage/clients_images/{$client->image}");
            } else {
                $client->image_url = null; // Set image URL to null if no image is available
            }
            return $client;
        });

        return response()->json($clients, 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        $client = Client::create($request->all());

        return response()->json(['message' => 'client created successfully', 'data' => $client], 201);
    }

    public function show($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'client not found'], 404);
        }
        return response()->json(['data' => $client], 200);
    }

    public function update(Request $request, $id)
    {
        $client = client::find($id);
        if (!$client) {
            return response()->json(['message' => 'client not found'], 404);
        }

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
        ]);

        $client->update($request->all());

        return response()->json(['message' => 'client updated successfully', 'data' => $client], 200);
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'client not found'], 404);
        }
        $client->delete();
        return response()->json(['message' => 'client deleted successfully'], 200);
    }

    public function getClientsCount()
    {

        $clients = Client::count();

        return response()->json($clients, 200);
    }

    public function searchClients(Request $request)
    {
        $keyword = $request->input('keyword');

        $clients = Client::where('name', 'like', "%$keyword%")
            ->paginate(5);

        return response()->json($clients, 200);
    }
}
