<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\RecoverPasswordSubmitRequest;
use Inertia\Inertia;
use Inertia\Response;

class RecoverPasswordController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.auth.recover-password.title'),
            'description' => trans('meta.admin.auth.recover-password.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/Auth/RecoverPassword', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of submit
     *
     * @param RecoverPasswordSubmitRequest $request
     * @return void
     */
    public function submit(RecoverPasswordSubmitRequest $request) {}
}
