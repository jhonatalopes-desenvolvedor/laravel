<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Overview;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class OverviewController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.overview.index.title'),
            'description' => trans('meta.admin.overview.index.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/Overview/Index', [
            'meta' => $this->meta(),
        ]);
    }

}
