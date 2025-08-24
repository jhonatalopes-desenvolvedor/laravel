<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Settings\Administrators;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\Administrators\AdministratorCreateSubmitRequest;
use Inertia\Inertia;
use Inertia\Response;

class AdministratorCreateController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.settings.administrators.create.title'),
            'description' => trans('meta.admin.settings.administrators.create.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render()
    {
        return Inertia::render('Admin/Settings/Administrators/Create', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of submit
     *
     * @param AdministratorCreateSubmitRequest $request
     * @return void
     */
    public function submit(AdministratorCreateSubmitRequest $request) {}
}
