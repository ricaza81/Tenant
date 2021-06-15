<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreTenantsRequest;
use App\Notifications\InvitationSend;
use Illuminate\Support\Facades\Gate;
use App\Property;
use App\Http\Controllers\Controller;
use App\User;

class TenantsController extends Controller
{

    /**
     * Display a listing of Tenants.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tenants = User::whereIn('property_id', Property::where('user_id', auth()->user()->id)->pluck('id'))->get();

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating new Tenant.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $properties = Property::where('user_id', auth()->user()->id)->pluck('name', 'id');

        return view('admin.tenants.create', compact('properties'));
    }

    /**
     * Store a newly created Tenant in storage.
     *
     * @param  \App\Http\Requests\StoreTenantsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTenantsRequest $request)
    {
        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => str_random(8),
            'property_id'      => $request->property_id,
            'invitation_token' => substr(md5(rand(0, 9) . $request->email . time()), 0, 32),
        ]);

        $user->role()->attach(3);

        $user->notify(new InvitationSend());

        return redirect()->route('admin.tenants.index');
    }

        /**
     * Show the form for editing Property.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //if (! Gate::allows('tenant_edit')) {
        //    return abort(401);
        //}

        //$tenant = 2;
        $property = Property::findOrFail($id);
        //$property = Property::where('user_id', auth()->user()->id)->pluck('name', 'id');

        //return view('admin.tenants.edit', compact('property'));
        return view('admin.tenants.edit', compact('property'));
    }

    /**
     * Update Property in storage.
     *
     * @param  \App\Http\Requests\UpdatePropertiesRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePropertiesRequest $request, $id)
    {
        if (! Gate::allows('property_edit')) {
            return abort(401);
        }

        $request  = $this->saveFiles($request);
        $property = Property::findOrFail($id);
        $property->update($request->all());

        return redirect()->route('admin.properties.index');
    }


    /**
     * Display Property.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('property_view')) {
            return abort(401);
        }

        $documents = \App\Document::where('property_id', $id)->get();
        $notes     = \App\Note::where('property_id', $id)->get();
        $property  = Property::findOrFail($id);

        return view('admin.properties.show', compact('property', 'documents', 'notes'));
    }


    /**
     * Remove Property from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->route('admin.properties.index');
    }

}
