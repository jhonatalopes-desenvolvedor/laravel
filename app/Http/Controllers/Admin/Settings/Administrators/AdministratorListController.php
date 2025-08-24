<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Settings\Administrators;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AdministratorListController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.settings.administrators.list.title'),
            'description' => trans('meta.admin.settings.administrators.list.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render()
    {
        return Inertia::render('Admin/Settings/Administrators/List', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of remove
     *
     * @param string $uuid
     * @return void
     */
    public function remove(string $uuid) {}
}
