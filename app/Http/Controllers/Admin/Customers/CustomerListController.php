<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Customers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CustomerListController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.customers.list.title'),
            'description' => trans('meta.admin.customers.list.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/Customers/List', [
            'meta' => $this->meta(),
        ]);
    }
}
