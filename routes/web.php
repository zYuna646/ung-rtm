<?php

use App\Livewire\Admin\Master\Fakultas\Index as FakultasIndex;
use App\Livewire\Admin\Master\Rtm\Detail;
use App\Livewire\Admin\Master\Rtm\Edit;
use App\Livewire\Admin\Master\Rtm\Index;
use App\Livewire\Admin\Master\Rtm\ViewSurvei;
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

use App\Livewire\Landing;
use App\Livewire\Auth;
use App\Livewire\Dashboard;
use App\Livewire\DownloadDocument;
use App\Livewire\MasterFakultas;
use App\Livewire\EditFakultas;
use App\Livewire\MasterProdi;
use App\Livewire\EditProdi;
use App\Livewire\MasterJurusan;
use App\Livewire\EditJurusan;
use App\Livewire\UserFakultas;
use App\Livewire\UserProdi;
use App\Livewire\EditUserProdi;
use App\Livewire\EditUserFakultas;
use App\Livewire\FakultasAudit;
use App\Livewire\MasterAkreditasi;
use App\Livewire\MasterAudit;
use App\Livewire\MasterPeriodeAudit;
use App\Livewire\MasterProdiAudit;
use App\Livewire\MasterSurvei;
use App\Livewire\UserProfile;



Route::get('/login', Auth::class)->name('login');
Route::get('/', Landing::class)->name('home');

Route::middleware(['auth'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', Dashboard::class)->name('index');

        Route::prefix('master')->name('master.')->group(function () {
            Route::prefix('fakultas')->name('fakultas.')->group(function () {
                Route::get('/', FakultasIndex::class)->name('index');
                Route::get('/edit/{id}', EditFakultas::class)->name('edit');
            });

            Route::prefix('prodi')->name('prodi.')->group(function () {
                Route::get('/', MasterProdi::class)->name('index');
                Route::get('/edit/{id}', EditProdi::class)->name('edit');
            });

            Route::prefix('rtm')->name('rtm.')->group(function () {
                Route::get('/', action: Index::class)->name('index');
                Route::get('/detail/{id}', action: Detail::class)->name('detail');
                Route::get('/edit/{id}', action: Edit::class)->name('edit');
                Route::get('/view-ami/{rtm_id}/{anchor_id}', \App\Livewire\Admin\Master\Rtm\ViewAmi::class)->name('view-ami');
                Route::get('/view-survei/{rtm_id}/{survei_id}', ViewSurvei::class)->name('view-survei');
                // Temuan context routes
                Route::get('/view-ami-temuan/{rtm_id}/{anchor_id}', \App\Livewire\Admin\Master\Rtm\ViewAmi::class)->name('view-ami-temuan');
                Route::get('/view-survei-temuan/{rtm_id}/{survei_id}', ViewSurvei::class)->name('view-survei-temuan');
                // Route::get('/download', action: Edit::class)->name('download');
            });

            // Route::prefix('survei')->name('survei.')->group(function () {
            //     Route::get('/', action: MasterSurvei::class)->name('index');
            // });

        });


        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/profile', UserProfile::class)->name('profile');
        });
    });



    // Route::get('/master_periode_audit', MasterPeriodeAudit::class)->name('master_periode_audit');
    // Route::get('/master_prodi_audit/{id_periode}', MasterProdiAudit::class)->name('master_prodi_audit');
    // Route::get('/master_audit/{id_periode}/{id_prodi}', MasterAudit::class)->name('master_audit');

    Route::get('/fakultas_audit', FakultasAudit::class)->name('fakultas_audit');
    Route::get('/departement_audit/{id_fakultas}', FakultasAudit::class)->name('departement_audit');

    Route::get('/download_document', DownloadDocument::class)->name('download_document');

    Route::get('/master_akreditasi', MasterAkreditasi::class)->name('master_akreditasi');

    Route::get('/user_prodi', UserProdi::class)->name('user_prodi');
    Route::get('/edit_user_prodi/{id}', EditUserProdi::class)->name('edit_user_prodi');
    Route::get('/user_fakultas', UserFakultas::class)->name('user_fakultas');
    Route::get('/edit_user_fakultas/{id}', EditUserFakultas::class)->name('edit_user_fakultas');
});
