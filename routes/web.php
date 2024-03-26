<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController as Login;
use App\Http\Controllers\DashboardController as Dashboard;
use App\Http\Controllers\InvoiceMailController as InvoiceMail;
use App\Http\Controllers\HistoryMailController as HistoryMail;
use App\Http\Controllers\HistoryWhatsappController as HistoryWhatsapp;
use App\Http\Controllers\OfficialReceiptController as OfficialReceipt;
use App\Http\Controllers\HistoryReceiptController as HistoryReceipt;
use App\Http\Controllers\HistoryReceiptWhatsappController as HistoryReceiptWhatsapp;
use App\Http\Controllers\AccountProfileController as AccountProfile;
use App\Http\Controllers\Admin\EmailConfigurationController as EmailConfiguration;

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

Route::controller(Login::class)->group(function () {
    Route::get('/', 'index')->name('/');
    Route::post('/login', 'authenticate')->name('login');
});

Route::group(['middleware' => ['auth.api', 'revalidate']], function () {
    Route::controller(Login::class)->group(function () {
        Route::get('/logout', 'logout')->name('logout');
    });

    Route::controller(Dashboard::class)->group(function () {
        Route::get('/dash', 'index')->name('dash');
        Route::post('/dash-getTable', 'getTable')->name('dash.getTable');
        Route::post('/dash-get-graph', 'show')->name('dash.get.graph');
        Route::get('/dash-topup', 'showTopUp')->name('dash.topup');
        Route::post('/dash-save-topup', 'store')->name('dash.save.topup');
        Route::get('/dash-how-to-topup', 'showHowTopup')->name('dash.how.to.topup');
    });

    Route::controller(InvoiceMail::class)->group(function () {
        Route::get('/index-invoice', 'index')->name('index.invoice');
        Route::view('/index-content-mail-invoice', 'content_email.invoice');
        Route::get('/table-invoice', 'getTable')->name('table.invoice');
        Route::get('/show-table-invoice-detail/{doc_no}', 'show')->name('show.table.invoice.detail.doc_no');
        Route::post('/submit-invoice', 'store')->name('submit.invoice');
        Route::post('/submit-wa-invoice', 'storeWA')->name('submit.wa.invoice');
        Route::post('/delete-invoice', 'destroy')->name('delete.invoice');
        Route::get('/index-popup-send-invoice', 'showPopupSend')->name('index.popup-send-invoice');
        Route::post('/submit-invoice-multi-platform', 'storeMultiPlatform')->name('submit.invoice.multi.platform');
    });

    Route::controller(HistoryMail::class)->group(function () {
        Route::get('/index-history', 'index')->name('index.history');
        Route::get('/table-history/{status}', 'getTable')->name('table.history.status');
        Route::get('/show-table-history-detail/{process_id}/{email_addr}', 'show')->name('show.table.history.detail.process_id.email_addr');
        Route::post('/submit-resend-invoice', 'store')->name('submit.resend.invoice');
    });

    Route::controller(HistoryWhatsapp::class)->group(function () {
        Route::get('/index-history-whatsapp', 'index')->name('index.history.whatsapp');
        Route::get('/table-history-whatsapp/{status}', 'getTable')->name('table.history.whatsapp.status');
        Route::get('/show-table-history-whatsapp-detail/{process_id}/{email_addr}', 'show')->name('show.table.history.whatsapp.detail.process_id.email_addr');
        Route::post('/submit-resend-whatsapp', 'store')->name('submit.resend.whatsapp');
    });

    Route::controller(OfficialReceipt::class)->group(function () {
        Route::get('/index-receipt', 'index')->name('index.receipt');
        Route::view('/index-content-mail-receipt', 'content_email.receipt');
        Route::get('/table-receipt', 'getTable')->name('table.receipt');
        Route::get('/show-table-receipt-detail/{doc_no}', 'show')->name('show.table.receipt.detail.doc_no');
        Route::post('/submit-receipt', 'store')->name('submit.receipt');
        Route::post('/stamp-receipt', 'storeStamp')->name('stamp.receipt');
        Route::post('/delete-receipt', 'destroy')->name('delete.receipt');
        Route::get('/index-popup-send-receipt', 'showPopupSend')->name('index.popup-send-receipt');
        Route::post('/submit-receipt-multi-platform', 'storeMultiPlatform')->name('submit.receipt.multi.platform');
    });

    Route::controller(HistoryReceipt::class)->group(function () {
        Route::get('/index-history-receipt', 'index')->name('index.history.receipt');
        Route::get('/table-history-receipt/{status}', 'getTable')->name('table.history.receipt.status');
        Route::get('/show-table-history-receipt-detail/{process_id}/{email_addr}', 'show')->name('show.table.history.receipt.detail.process_id.email_addr');
        Route::post('/submit-resend-receipt', 'store')->name('submit.resend.receipt');
    });

    Route::controller(HistoryReceiptWhatsapp::class)->group(function () {
        Route::get('/index-history-receipt-whatsapp', 'index')->name('index.history.receipt.whatsapp');
        Route::get('/table-history-receipt-whatsapp/{status}', 'getTable')->name('table.history.receipt.whatsapp.status');
        Route::get('/show-table-history-receipt-whatsapp-detail/{process_id}/{email_addr}', 'show')->name('show.table.history.receipt.whatsapp.detail.process_id.email_addr');
        Route::post('/submit-resend-receipt-whatsapp', 'store')->name('submit.resend.whatsapp');
    });

    Route::controller(EmailConfiguration::class)->middleware('admin')->group(function () {
        Route::get('/index-config', 'index')->name('index.config');
        Route::get('/show-config', 'show')->name('show.config');
        Route::post('/submit-config', 'store')->name('submit.config');
    });

    Route::controller(AccountProfile::class)->group(function () {
        Route::get('/index-account', 'index')->name('index.account');
        Route::post('/submit-change-pass', 'store')->name('submit.change.pass');
    });
});
