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
            Route::get('enableapi', 'DubaiOrdersController@enableapi')->name('enableapi');
            Route::get('enablenotapi', 'DubaiOrdersController@enablenotapi')->name('enablenotapi');



           
             //dubiorders routes
             Route::resource('dubiorders', 'DubaiOrdersController');
             Route::resource('dubioff', 'DubaiOffController');
             Route::get('/dubioff/{id}', 'DubaiOffController@show')->name('dubioff.products');
             Route::get('/disabledubioff/{id}', 'DubaiOffController@disabledubioff')->name('dubidisable');
             Route::get('/enabledubioff/{id}', 'DubaiOffController@enabledubioff')->name('dubienable');

             Route::get('/dubidisablecard/{id}', 'DubaiOffController@dubidisablecard')->name('dubidisablecard');
             Route::get('/dubienablecard/{id}', 'DubaiOffController@dubienablecard')->name('dubienablecard');

///////////////////////////////////////////localcompany controle
             Route::resource('localcompany', 'localcompanyController');
             Route::get('/localoff/{id}', 'localcompanyController@show')->name('localoff.products');
             Route::get('/localdisable/{id}', 'localcompanyController@disabledubioff')->name('localdisable');
             Route::get('/localenable/{id}', 'localcompanyController@enabledubioff')->name('localenable');
             Route::get('/localdisablecard/{id}', 'localcompanyController@dubidisablecard')->name('localdisablecard');
             Route::get('/localenablecard/{id}', 'localcompanyController@dubienablecard')->name('localenablecard');

///////////////////////////////////////////nationalcompany controle
Route::resource('nationalcompany', 'nationalcompanyController');
Route::get('/nationaloff/{id}', 'nationalcompanyController@show')->name('nationaloff.products');
Route::get('/nationaldisable/{id}', 'nationalcompanyController@disabledubioff')->name('nationaldisable');
Route::get('/nationalenable/{id}', 'nationalcompanyController@enabledubioff')->name('nationalenable');
Route::get('/nationaldisablecard/{id}', 'nationalcompanyController@dubidisablecard')->name('nationaldisablecard');
Route::get('/nationalenablecard/{id}', 'nationalcompanyController@dubienablecard')->name('nationalenablecard');








           //  Route::get('/dubiorders/{order}/products', 'DubaiOrdersController@products')->name('dubiorders.products');
            //currancy routes
            Route::resource('currancy', 'currancyController');
          
            //user routes
            Route::resource('users', 'UserController')->except(['show']);

            Route::get('checkpdf', 'CompanyController@generate_pdf')->name('checkpdf');


        });//end of dashboard routes
    });


