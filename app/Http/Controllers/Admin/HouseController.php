<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\House;
use App\Models\Service;
use App\Models\Sponsor;
use App\Functions\Helper;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Request\HouseRequest;

class HouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $houses = House::where('user_id', Auth::user()->id)->paginate(5);
        return view('admin.houses.index', compact('houses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all();

        return view('admin.houses.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HouseRequest $request)
    {
        $val_data = $request->all();
        $val_data['user_id'] = Auth::user()->id;
        $val_data['slug'] = Helper::generateSlug($val_data['title'], House::class);

        $house = new House();
        $house->fill($val_data);
        $house->save();

        return redirect()->route('admin.houses.show', compact('house'));
        // aggiungere i servizi col with
    }

    /**
     * Display the specified resource.
     */
    public function show(House $house)
    {
        return view('admin.houses.show', compact('house'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(House $house)
    {
        $services = Service::all();
        return view('admin.houses.edit', compact('house', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, House $house)
    {
        $val_data = $request->all();
        $val_data['user_id'] = Auth::user()->id;
        $val_data['slug'] = Helper::generateSlug($val_data['title'], House::class);

        $house->update($val_data);

        return redirect()->route('admin.houses.show', compact('house'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(House $house)
    {
        $house->delete();
        return redirect()->route('admin.houses.index');
    }
}
