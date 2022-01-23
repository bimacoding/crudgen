<?php
Route::group(['middleware' => ['web','auth']], function() {
    Route::get('/crudgenerator',function(){
        return view('crud::index');
    });
    Route::post('/crud/insert',[\Erendi\Crudgenerator\CrudController::class,'insert'])->name('crud.insert');
});
