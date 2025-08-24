<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Admin\MyAccount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MyAccount\ChangeProfileSubmitRequest;
use Inertia\Inertia;
use Inertia\Response;

class ChangeProfileController extends Controller
{
    /**
     * Summary of meta
     *
     * @return array{description: string, title: string}
     */
    private function meta(): array
    {
        return [
            'title'       => trans('meta.admin.my-account.change-profile.title'),
            'description' => trans('meta.admin.my-account.change-profile.description'),
        ];
    }

    /**
     * Summary of render
     *
     * @return Response
     */
    public function render(): Response
    {
        return Inertia::render('Admin/MyAccount/ChangeProfile', [
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Summary of submit
     *
     * @param ChangeProfileSubmitRequest $request
     * @return void
     */
    public function submit(ChangeProfileSubmitRequest $request) {}
}
