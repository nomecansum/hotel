<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Planta\BulkDestroyPlanta;
use App\Http\Requests\Admin\Planta\DestroyPlanta;
use App\Http\Requests\Admin\Planta\IndexPlanta;
use App\Http\Requests\Admin\Planta\StorePlanta;
use App\Http\Requests\Admin\Planta\UpdatePlanta;
use App\Models\Planta;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PlantasController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexPlanta $request
     * @return array|Factory|View
     */
    public function index(IndexPlanta $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Planta::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['des_planta', 'id_cliente', 'id_edificio', 'id_planta'],

            // set columns to searchIn
            ['des_planta']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.planta.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.planta.create');

        return view('admin.planta.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePlanta $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StorePlanta $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Planta
        $plantum = Planta::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/plantas'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/plantas');
    }

    /**
     * Display the specified resource.
     *
     * @param Planta $plantum
     * @throws AuthorizationException
     * @return void
     */
    public function show(Planta $plantum)
    {
        $this->authorize('admin.planta.show', $plantum);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Planta $plantum
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Planta $plantum)
    {
        $this->authorize('admin.planta.edit', $plantum);


        return view('admin.planta.edit', [
            'plantum' => $plantum,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePlanta $request
     * @param Planta $plantum
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePlanta $request, Planta $plantum)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Planta
        $plantum->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/plantas'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/plantas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPlanta $request
     * @param Planta $plantum
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyPlanta $request, Planta $plantum)
    {
        $plantum->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPlanta $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPlanta $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Planta::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
