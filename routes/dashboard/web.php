<?php

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']],
    function () {

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

            Route::get('/', 'WelcomeController@index')->name('index');

            //Companies routes
            Route::resource('Companies', 'CompanyController')->except(['show']);

            //Cards routes
            Route::resource('Cards', 'CardsController')->except(['show']);
            
            Route::get('offer/{id}', 'CardsController@offer')->name('offer');
            Route::get('notoffer/{id}', 'CardsController@notoffer')->name('notoffer');
            Route::post('importcard', 'CardsController@import')->name('importcard');
            Route::any('Cards/compcard/{id}','CardsController@cmpanies')->name('Cards/compcard');


            //client routes
            Route::resource('clients', 'ClientController')->except(['show']);
            Route::resource('clients.orders', 'Client\OrderController')->except(['show']);

            //order routes
            Route::resource('orders', 'OrderController');
            Route::get('/orders/{order}/products', 'OrderController@products')->name('orders.products');
            Route::get('/dubiorders/{order}', 'DubaiOrdersController@dubiorders')->name('dubiorders.products');
            Route::get('enableapi', 'OrderController@enableapi')->name('enableapi');
            Route::get('enablenotapi', 'OrderController@enablenotapi')->name('enablenotapi');
             //dubiorders routes
             Route::resource('dubiorders', 'DubaiOrdersController');
           //  Route::get('/dubiorders/{order}/products', 'DubaiOrdersController@products')->name('dubiorders.products');
            //currancy routes
            Route::resource('currancy', 'currancyController');
          
            //user routes
            Route::resource('users', 'UserController')->except(['show']);

            Route::get('checkpdf', 'CompanyController@generate_pdf')->name('checkpdf');


        });//end of dashboard routes
    });


