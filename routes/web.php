<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UsersController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth', 'role:admin')->group(function () {
    Route::resource('user', UsersController::class)->except(['show']);
    Route::get('users/data-table', [UsersController::class, 'dataTable'])->name('users.data');
    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::get('/search-users', [RoleController::class, 'search'])->name('users.search');
    Route::get('/role/get-roles-permissions/{id}', [RoleController::class, 'getRolesPermissions'])->name('role.getRolesPermissions');
    Route::post('/role/assign-roles-permissions/{id}', [RoleController::class, 'assignRolesPermissions'])->name('role.assignRolesPermissions');
});

Route::middleware('auth')->group(
    function () {
        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard.index');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::get('categories/data-table', [CategoryController::class, 'dataTable'])->name('categories.data');

        Route::resource('product', ProductController::class)->except(['show']);
        Route::get('product/data-table', [ProductController::class, 'dataTable'])->name('product.data');

        Route::resource('transaction', TransactionController::class)->except(['show']);
        Route::get('transaction/data-table', [TransactionController::class, 'dataTable'])->name('transaction.data');
        Route::post('/midtrans-callback', [TransactionController::class, 'handleCallback'])->name('midtrans.callback');



        Route::get('/chart', function () {
            return view('backend.pages.charts.chartjs');
        })->name('chart.index');

        Route::get('/icon', function () {
            return view('backend.pages.icons.mdi');
        })->name('icon.index');
    }


);
