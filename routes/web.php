<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laporanController;

Route::resource('laporan', laporanController::class);
Route::get('/', [laporanController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard', [laporanController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard/datatables', [laporanController::class, 'dashboardDatatables'])->name('dashboard.datatables');
Route::get('/laporan', [laporanController::class, 'create'])->name('laporan.create');
Route::get('/sejarah', [laporanController::class, 'index'])->name('sejarah');
Route::get('/create', [laporanController::class, 'create'])->name('index.create');
Route::post('/store', [laporanController::class, 'store'])->name('index.store');
Route::get('/edit{id}', [laporanController::class, 'edit'])->name('index.edit');
Route::put('/update{id}', [laporanController::class, 'update'])->name('index.update');
Route::delete('/laporan/{id}/delete', [laporanController::class, 'destroy'])->name('laporan.destroy');
Route::put('/laporan/{id}', [laporanController::class, 'update'])->name('laporan.update');
Route::get('/laporan/{id}/tindakan', [laporanController::class, 'tindakan'])->name('laporan.tindakan');
Route::post('/laporan/{id}/tindakan', [laporanController::class, 'storeTindakan'])->name('laporan.storeTindakan');
Route::get('/get-supervisor/{id}', [laporanController::class, 'getSupervisor'])->name('get.supervisor');
Route::get('/sejarah/datatables', [laporanController::class, 'sejarahDatatables'])->name('sejarah.datatables');
Route::get('/laporan/penyelesaian/{id}', [laporanController::class, 'getPenyelesaian']);
Route::get('/sejarah/download', [laporanController::class, 'downloadSejarah'])->name('sejarah.download');