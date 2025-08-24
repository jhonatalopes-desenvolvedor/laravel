<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Barbers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Barbers\BarberCreateSubmitRequest;
use Inertia\Inertia;
use Inertia\Response;

class BarberCreateController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.barbers.create.title'),
            'description' => trans('meta.admin.barbers.create.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/Barbers/Create', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of submit
     *
     * @param BarberCreateSubmitRequest $request
     * @return void
     */
    public function submit(BarberCreateSubmitRequest $request) {}
}
