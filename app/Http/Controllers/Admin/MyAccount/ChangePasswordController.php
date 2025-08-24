<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\MyAccount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MyAccount\ChangePasswordSubmitRequest;
use Inertia\Inertia;
use Inertia\Response;

class ChangePasswordController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.my-account.change-password.title'),
            'description' => trans('meta.admin.my-account.change-password.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/MyAccount/ChangePassword', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of submit
     *
     * @param ChangePasswordSubmitRequest $request
     * @return void
     */
    public function submit(ChangePasswordSubmitRequest $request) {}
}
