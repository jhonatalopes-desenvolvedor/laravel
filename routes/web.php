<?php

declare(strict_types = 1);

use App\Mcp\Tools\ProjectContext;
use Illuminate\Support\Facades\Route;

Route::name('mcp.')->prefix('/mcp')->group(function () {
    Route::get('/project-context', function () {
        return (new ProjectContext(base_path()))->get();
    });
});

Route::name('web.admin.')->prefix('/admin')->middleware([])->group(function () {
    Route::controller(App\Http\Controllers\Admin\Auth\LoginController::class)->name('login.')->prefix('/logar')->group(function () {
        Route::get('/', 'render')->name('render');
        Route::post('/', 'submit')->name('submit')->middleware(['precognitive']);
    });

    Route::controller(App\Http\Controllers\Admin\Auth\RecoverPasswordController::class)->name('recover-password.')->prefix('/recuperar-senha')->group(function () {
        Route::get('/', 'render')->name('render');
        Route::post('/', 'submit')->name('submit')->middleware(['precognitive']);
    });
});

Route::name('web.admin')->prefix('/admin')->middleware([])->group(function () {
    Route::controller(App\Http\Controllers\Admin\Overview\OverviewController::class)->name('overview.')->group(function () {
        Route::get('/', 'render')->name('render');
    });

    Route::name('my-account.')->prefix('/minha-conta')->group(function () {
        Route::controller(App\Http\Controllers\Admin\MyAccount\ChangeProfileController::class)->name('change-profile.')->prefix('/alterar-perfil')->group(function () {
            Route::get('/', 'render')->name('render');
            Route::post('/', 'submit')->name('submit')->middleware(['precognitive']);
        });

        Route::controller(App\Http\Controllers\Admin\MyAccount\ChangePasswordController::class)->name('change-password.')->prefix('/alterar-senha')->group(function () {
            Route::get('/', 'render')->name('render');
            Route::post('/', 'submit')->name('submit')->middleware(['precognitive']);
        });

        Route::post('/deslogar', App\Http\Controllers\Admin\MyAccount\LogoutController::class)->name('logout');
    });

    Route::name('settings.')->prefix('/configuracoes')->group(function () {
        Route::controller(App\Http\Controllers\Admin\Settings\Overview\OverviewSettingController::class)->name('overview.')->group(function () {
            Route::get('/', 'render')->name('render');
        });

        Route::name('administrators.')->prefix('/administradores')->group(function () {
            Route::controller(App\Http\Controllers\Admin\Settings\Administrators\AdministratorListController::class)->name('list.')->group(function () {
                Route::get('/', 'render')->name('render');
                Route::delete('/{uuid}/remover', 'remove')->name('remove');
            });

            Route::controller(App\Http\Controllers\Admin\Settings\Administrators\AdministratorCreateController::class)->name('create.')->prefix('/criar')->group(function () {
                Route::get('/', 'render')->name('render');
                Route::post('/', 'submit')->name('submit')->middleware(['precognitive']);
            });

            Route::name('manage.')->prefix('/gerenciar/{uuid}')->group(function () {
                Route::controller(App\Http\Controllers\Admin\Settings\Administrators\AdministratorSummaryTabController::class)->name('summary-tab.')->prefix('/resumo')->group(function () {
                    Route::get('/', 'render')->name('render');
                });
            });
        });
    });

});
