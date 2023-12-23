<?php

use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\DefaultCategoryController;
use App\Http\Controllers\api\TransactionController;
use App\Http\Controllers\api\VcardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Middleware\DenyBlockedUsers;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login'])->middleware(DenyBlockedUsers::class);
Route::post('vcards', [VcardController::class, 'store']);
Route::get('vcards/share/{vcard}', [VcardController::class, 'share']);

Route::get('vcards/share/{vcard}', [VcardController::class, 'share']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users/me', [UserController::class, 'show_me']);

    Route::controller(VcardController::class)->group(function () {
        Route::get('vcards/statactive', 'totalNumOfActiveVCards')
            ->middleware('can:stats,App\Models\Admin');
        Route::get('vcards/statblocked', 'totalNumOfBlockedVCards')
            ->middleware('can:stats,App\Models\Admin');
        Route::get('vcards/stat', 'totalNumOfVCards')
            ->middleware('can:stats,App\Models\Admin');
        Route::get('vcards', 'index')
            ->middleware('can:viewAny,App\Models\Vcard');
        Route::get('vcards/{vcard}', 'show')
            ->middleware('can:view,vcard');
        Route::put('vcards/{vcard}', 'update')
            ->middleware('can:update,vcard');
        Route::patch('vcards/{vcard}/block', 'block')
            ->middleware('can:admin,vcard');
        Route::patch('vcards/{vcard}/max_debit', 'updateLimit')
            ->middleware('can:admin,vcard');
        Route::patch('vcards/{vcard}/password', 'update_password')
            ->middleware('can:updateCredentials,vcard');
        Route::patch('vcards/{vcard}/confirmation_code', 'update_confirmationcode')
            ->middleware('can:updateCredentials,vcard');
        Route::post('vcards/{vcard}/verify', 'verifyCredentials')
            ->middleware('can:updateCredentials,vcard');
        Route::delete('vcards/{vcard}', 'destroy')
            ->middleware('can:delete,vcard');     
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('categories', 'index')
            ->middleware('can:viewAny,App\Models\Category');
        Route::get('categories/{vcard}/stats', 'showVcardTotalCategoriesForVCard')
            ->middleware('can:stats,vcard');
        Route::get('categories/{category}', 'show')
            ->middleware('can:view,category');
        Route::post('categories', 'store')
            ->middleware('can:create,App\Models\Category');
        Route::put('categories/{category}', 'update')
            ->middleware('can:update,category');
        Route::delete('categories/{category}', 'destroy')
            ->middleware('can:delete,category');
    });

    Route::controller(DefaultCategoryController::class)->group(function () {
        Route::get('defaultcategories', 'index')
            ->middleware('can:viewAny,App\Models\DefaultCategory');
        Route::get('defaultcategories/{defaultcategory}', 'show')
            ->middleware('can:view,defaultcategory');
        Route::post('defaultcategories', 'store')
            ->middleware('can:create,App\Models\DefaultCategory');
        Route::put('defaultcategories/{defaultcategory}', 'update')
            ->middleware('can:update,defaultcategory');
        Route::delete('defaultcategories/{defaultcategory}', 'destroy')
            ->middleware('can:delete,defaultcategory');
    });

    Route::controller(TransactionController::class)->group(function () {
        Route::get('transactions', 'index')
            ->middleware('can:viewAny,App\Models\Transaction');
        Route::get('transactions/statcredit', 'showCountCreditTransactions')
            ->middleware('can:stats,App\Models\Admin');
        Route::get('transactions/statdebit', 'showCountDebitTransactions')
            ->middleware('can:stats,App\Models\Admin');
        Route::get('transactions/vcard/{vcard}/stats', 'showVcardTransactionsStats')
            ->middleware('can:stats,vcard');
        Route::get('transactions/vcard/{vcard}/totalNumActions', 'showVcardTotalTransactions')
        ->middleware('can:stats,vcard');
        Route::get('transactions/{transaction}', 'show')
            ->middleware('can:view,transaction');
        Route::post('transactions/debit', 'storeDebit')
            ->middleware('can:createDebit,App\Models\Transaction');
        Route::post('transactions/credit', 'storeCredit')
            ->middleware('can:createCredit,App\Models\Transaction');
        Route::put('transactions/{transaction}', 'update')
            ->middleware('can:update,transaction');
    });

    Route::controller(AdminController::class)->group(function () {
        Route::get('admins', 'index')
            ->middleware('can:viewAny,App\Models\Admin');
        Route::get('admins/stat', 'totalNumOfAdmins')
            ->middleware('can:stats,App\Models\Admin');
        Route::get('admins/{admin}', 'show')
            ->middleware('can:view,admin');
        Route::post('admins', 'store')
            ->middleware('can:create,App\Models\Admin');
        Route::put('admins/{admin}', 'update')
            ->middleware('can:update,admin');
        Route::patch('admins/{admin}/password', 'update_password')
            ->middleware('can:updatePassword,admin');
        Route::delete('admins/{admin}', 'destroy')
            ->middleware('can:delete,admin');
    });

});