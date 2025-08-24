<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Customers\CustomerCreateSubmitRequest;
use Inertia\Inertia;
use Inertia\Response;

class CustomerCreateController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.customers.create.title'),
            'description' => trans('meta.admin.customers.create.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/Customers/Create', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of submit
     *
     * @param CustomerCreateSubmitRequest $request
     * @return void
     */
    public function submit(CustomerCreateSubmitRequest $request) {}
}
