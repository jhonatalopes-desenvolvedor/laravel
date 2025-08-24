<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginSubmitRequest;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.auth.login.title'),
            'description' => trans('meta.admin.auth.login.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/Auth/Login', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of submit
     *
     * @param LoginSubmitRequest $request
     * @return void
     */
    public function submit(LoginSubmitRequest $request) {}
}
