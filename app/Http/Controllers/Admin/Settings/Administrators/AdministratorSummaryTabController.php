<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Settings\Administrators;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AdministratorSummaryTabController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.settings.administrators.summary-tab.title'),
            'description' => trans('meta.admin.settings.administrators.summary-tab.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render()
    {
        return Inertia::render('Admin/Settings/Administrators/SummaryTab', [
            'meta' => $this->meta(),
        ]);
    }
}
