<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Barbers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class BarberListController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.barbers.list.title'),
            'description' => trans('meta.admin.barbers.list.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/Barbers/List', [
            'meta' => $this->meta(),
        ]);
    }
}
