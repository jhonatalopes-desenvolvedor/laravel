<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Settings\Overview;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class OverviewSettingController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.settings.overview.index.title'),
            'description' => trans('meta.admin.settings.overview.index.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render()
    {
        return Inertia::render('Admin/Settings/Overview/Index', [
            'meta' => $this->meta(),
        ]);
    }

}
