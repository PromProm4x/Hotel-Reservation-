<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', [
            'services' => Service::query()->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request)
    {
        Service::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(StoreServiceRequest $request, Service $service)
    {
        $service->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete(); // soft delete — folio snapshots are kept

        return redirect()
            ->route('admin.services.index')
            ->with('status', 'Service deleted. Existing folio charges are kept.');
    }
}
