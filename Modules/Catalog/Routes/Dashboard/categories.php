<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'categories'], function () {

  	Route::get('/' ,'Dashboard\CategoryController@index')
  	->name('dashboard.categories.index')
    ->middleware(['permission:show_categories']);

  	Route::get('datatable'	,'Dashboard\CategoryController@datatable')
  	->name('dashboard.categories.datatable')
  	->middleware(['permission:show_categories']);
	
	  Route::get('exports/{pdf}' , 'Dashboard\CategoryController@export')
	  ->name('dashboard.categories.export')
	  ->middleware(['permission:show_categories']);

  	Route::get('create'		,'Dashboard\CategoryController@create')
  	->name('dashboard.categories.create')
    ->middleware(['permission:add_categories']);

    //import excel
    Route::post('import', 'Dashboard\CategoryController@Import')
        ->name('dashboard.categories.import.excel')
        ->middleware(['permission:add_categories']);


  	Route::post('/'			,'Dashboard\CategoryController@store')
  	->name('dashboard.categories.store')
    ->middleware(['permission:add_categories']);

  	Route::get('{id}/edit'	,'Dashboard\CategoryController@edit')
  	->name('dashboard.categories.edit')
    ->middleware(['permission:edit_categories']);

    Route::post('/update/photo', 'Dashboard\CategoryController@updatePhoto')
        ->name('dashboard.categories.update.photo')
        ->middleware(['permission:edit_categories']);

  	Route::put('{id}'		,'Dashboard\CategoryController@update')
  	->name('dashboard.categories.update')
    ->middleware(['permission:edit_categories']);

  	Route::delete('{id}'	,'Dashboard\CategoryController@destroy')
  	->name('dashboard.categories.destroy')
    ->middleware(['permission:delete_categories']);

  	Route::get('deletes'	,'Dashboard\CategoryController@deletes')
  	->name('dashboard.categories.deletes')
    ->middleware(['permission:delete_categories']);

  	Route::get('{id}','Dashboard\CategoryController@show')
  	->name('dashboard.categories.show')
    ->middleware(['permission:show_categories']);

});

Route::group(['prefix' => 'brands'], function () {

  	Route::get('/' ,'Dashboard\BrandController@index')
  	->name('dashboard.brands.index')
    ->middleware(['permission:show_brands']);

  	Route::get('datatable'	,'Dashboard\BrandController@datatable')
  	->name('dashboard.brands.datatable')
  	->middleware(['permission:show_brands']);

  	Route::get('create'		,'Dashboard\BrandController@create')
  	->name('dashboard.brands.create')
    ->middleware(['permission:add_brands']);

  	Route::post('/'			,'Dashboard\BrandController@store')
  	->name('dashboard.brands.store')
    ->middleware(['permission:add_brands']);

  	Route::get('{id}/edit'	,'Dashboard\BrandController@edit')
  	->name('dashboard.brands.edit')
    ->middleware(['permission:edit_brands']);

  	Route::put('{id}'		,'Dashboard\BrandController@update')
  	->name('dashboard.brands.update')
    ->middleware(['permission:edit_brands']);

  	Route::delete('{id}'	,'Dashboard\BrandController@destroy')
  	->name('dashboard.brands.destroy')
    ->middleware(['permission:delete_brands']);

  	Route::get('deletes'	,'Dashboard\BrandController@deletes')
  	->name('dashboard.brands.deletes')
    ->middleware(['permission:delete_brands']);

  	Route::get('{id}','Dashboard\BrandController@show')
  	->name('dashboard.brands.show')
    ->middleware(['permission:show_brands']);

});
