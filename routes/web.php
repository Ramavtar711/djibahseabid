<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\BuyerPaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QcController;
use App\Http\Controllers\SellerController;
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

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/home', [HomeController::class, 'index'])->name('home.home');
Route::get('/login', [HomeController::class, 'login'])->name('home.login');
Route::post('/login', [HomeController::class, 'loginStore'])->name('home.login.store');
Route::get('/buyer-register', [HomeController::class, 'buyerRegister'])->name('home.buyer-register');
Route::post('/buyer-register', [HomeController::class, 'buyerRegisterStore'])->name('home.buyer-register.store');
Route::get('/seller-register', [HomeController::class, 'sellerRegister'])->name('home.seller-register');
Route::post('/seller-register', [HomeController::class, 'sellerRegisterStore'])->name('home.seller-register.store');

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/live-auction', [AdminController::class, 'liveAuction'])->name('admin.live-auction');
    Route::get('/upcoming-auction', [AdminController::class, 'upcomingAuction'])->name('admin.upcoming-auction');
    Route::get('/lot-management', [AdminController::class, 'lotManagement'])->name('admin.lot-management');
    Route::get('/create-lot', [AdminController::class, 'createLot'])->name('admin.create-lot');
    Route::get('/lot-details', [AdminController::class, 'lotDetails'])->name('admin.lot-details');
    Route::get('/buyers', [AdminController::class, 'buyers'])->name('admin.buyers');
    Route::get('/buyer-details', [AdminController::class, 'buyerDetails'])->name('admin.buyer-details');
    Route::get('/add-buyer', [AdminController::class, 'addBuyer'])->name('admin.add-buyer');
    Route::get('/sellers', [AdminController::class, 'sellers'])->name('admin.sellers');
    Route::get('/seller-details', [AdminController::class, 'sellerDetails'])->name('admin.seller-details');
    Route::get('/add-seller', [AdminController::class, 'addSeller'])->name('admin.add-seller');
    Route::get('/finance-overview', [AdminController::class, 'financeOverview'])->name('admin.finance-overview');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::get('/account-settings', [AdminController::class, 'accountSettings'])->name('admin.account-settings');
    Route::get('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'loginStore'])->name('admin.login.store');
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('admin.transactions');
    Route::get('/bank-transfer', [AdminController::class, 'bankTransfer'])->name('admin.bank-transfer');
    Route::get('/risk-monitoring', [AdminController::class, 'riskMonitoring'])->name('admin.risk-monitoring');
    Route::get('/alets', [AdminController::class, 'alets'])->name('admin.alets');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
});

Route::prefix('qc')->group(function () {
    Route::get('/', [QcController::class, 'index'])->name('qc.index');
    Route::get('/dashboard', [QcController::class, 'dashboard'])->name('qc.dashboard');
    Route::get('/lot-submitted', [QcController::class, 'lotSubmitted'])->name('qc.lot-submitted');
    Route::get('/lot-subimitted-details', [QcController::class, 'lotSubmittedDetails'])->name('qc.lot-subimitted-details');
    Route::get('/auction-setup', [QcController::class, 'auctionSetup'])->name('qc.auction-setup');
    Route::post('/auction-setup', [QcController::class, 'storeAuctionSetup'])->name('qc.auction-setup.update');
    Route::get('/auction-scheduled', [QcController::class, 'auctionScheduled'])->name('qc.auction-scheduled');
    Route::post('/auction-scheduled', [QcController::class, 'storeAuctionScheduled'])->name('qc.auction-scheduled.update');
    Route::get('/media-control', [QcController::class, 'mediaControl'])->name('qc.media-control');
    Route::post('/media-control', [QcController::class, 'storeMediaControl'])->name('qc.media-control.update');
    Route::get('/notifications', [QcController::class, 'notifications'])->name('qc.notifications');
    Route::get('/notifications/data', [QcController::class, 'notificationData'])->name('qc.notifications.data');
    Route::post('/notifications/mark-read', [QcController::class, 'markNotificationRead'])->name('qc.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [QcController::class, 'markAllNotificationsRead'])->name('qc.notifications.mark-all-read');
    Route::get('/permissions', [QcController::class, 'permissions'])->name('qc.permissions');
    Route::get('/account-settings', [QcController::class, 'accountSettings'])->name('qc.account-settings');
    Route::get('/login', [QcController::class, 'login'])->name('qc.login');
});

Route::prefix('seller')->group(function () {
    Route::get('/', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard-page');
    Route::get('/create-lot', [SellerController::class, 'createLot'])->name('seller.create-lot');
    Route::post('/create-lot', [SellerController::class, 'storeLot'])->name('seller.store-lot');
    Route::get('/lot-list', [SellerController::class, 'lotList'])->name('seller.lot-list');
    Route::get('/lot-details/{lot?}', [SellerController::class, 'lotDetails'])->name('seller.lot-details');
    Route::get('/live-view', [SellerController::class, 'liveView'])->name('seller.live-view');
    Route::get('/active-auction', [SellerController::class, 'activeAuction'])->name('seller.active-auction');
    Route::get('/pending-validation', [SellerController::class, 'pendingValidation'])->name('seller.pending-validation');
    Route::get('/approve-lots', [SellerController::class, 'approveLots'])->name('seller.approve-lots');
    Route::get('/sold-lots', [SellerController::class, 'soldLots'])->name('seller.sold-lots');
    Route::get('/revenue', [SellerController::class, 'revenue'])->name('seller.revenue');
    Route::post('/relist-lot', [SellerController::class, 'relistLot'])->name('seller.relist-lot');
    Route::get('/notifications', [SellerController::class, 'notifications'])->name('seller.notifications');
    Route::get('/notifications/data', [SellerController::class, 'notificationData'])->name('seller.notifications.data');
    Route::post('/notifications/mark-read', [SellerController::class, 'markNotificationRead'])->name('seller.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [SellerController::class, 'markAllNotificationsRead'])->name('seller.notifications.mark-all-read');
    Route::get('/auction-status', [SellerController::class, 'auctionStatus'])->name('seller.auction-status');
    Route::get('/active-auctions/data', [SellerController::class, 'activeAuctionsData'])->name('seller.active-auctions.data');
});

Route::prefix('buyer')->group(function () {
    Route::get('/', [BuyerController::class, 'dashboard'])->name('buyer.dashboard');
    Route::get('/dashboard', [BuyerController::class, 'dashboard'])->name('buyer.dashboard-page');
    Route::get('/profile-settings', [BuyerController::class, 'profileSettings'])->name('buyer.profile-settings');
    Route::post('/profile-settings', [BuyerController::class, 'updateProfileSettings'])->name('buyer.profile-settings.update');
    Route::get('/active-auction', [BuyerController::class, 'activeAuction'])->name('buyer.active-auction');
    Route::get('/upcoming-auction', [BuyerController::class, 'upcomingAuction'])->name('buyer.upcoming-auction');
    Route::get('/live-auction', [BuyerController::class, 'liveAuction'])->name('buyer.live-auction');
    Route::get('/won-auction', [BuyerController::class, 'wonAuction'])->name('buyer.won-auction');
    Route::get('/transactions', [BuyerController::class, 'transactions'])->name('buyer.transactions');
    Route::post('/place-bid', [BuyerController::class, 'placeBid'])->name('buyer.place-bid');
    Route::post('/upcoming-auction/remind/{lot}', [BuyerController::class, 'remindUpcomingAuction'])->name('buyer.upcoming-auction.remind');
    Route::get('/live-auction/data', [BuyerController::class, 'liveAuctionData'])->name('buyer.live-auction.data');
    Route::post('/live-auction/chat', [BuyerController::class, 'postAuctionMessage'])->name('buyer.live-auction.chat');
    Route::get('/payments/checkout/{settlement}', [BuyerPaymentController::class, 'checkoutSettlement'])->name('buyer.payments.checkout');
    Route::get('/payments/success', [BuyerPaymentController::class, 'checkoutSuccess'])->name('buyer.payments.success');
    Route::get('/payments/cancel', [BuyerPaymentController::class, 'checkoutCancel'])->name('buyer.payments.cancel');
    Route::get('/payments/details/{settlement}', [BuyerPaymentController::class, 'show'])->name('buyer.payments.show');
    Route::get('/payments/details/{settlement}/invoice', [BuyerPaymentController::class, 'invoice'])->name('buyer.payments.invoice');
    Route::get('/payments/details/{settlement}/receipt', [BuyerPaymentController::class, 'receipt'])->name('buyer.payments.receipt');
    Route::post('/payments/topup', [BuyerPaymentController::class, 'checkoutTopup'])->name('buyer.payments.topup');
    Route::post('/payments/wallet/{settlement}', [BuyerPaymentController::class, 'payWithWallet'])->name('buyer.payments.wallet');
    Route::post('/payments/bank-transfer/{settlement}', [BuyerPaymentController::class, 'payWithBankTransfer'])->name('buyer.payments.bank-transfer');
    Route::post('/payments/waafipay/{settlement}', [BuyerPaymentController::class, 'payWithWaafiPay'])->name('buyer.payments.waafipay');
    Route::get('/notifications/data', [BuyerController::class, 'notificationData'])->name('buyer.notifications.data');
    Route::post('/notifications/mark-read', [BuyerController::class, 'markNotificationRead'])->name('buyer.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [BuyerController::class, 'markAllNotificationsRead'])->name('buyer.notifications.mark-all-read');
});
