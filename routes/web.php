<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This Comment is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


// Main Page
Route::get('dashboard', ['as' => 'dashboard', 'uses' => function() {
    if (Sentinel::check()) {
        return view('dashboard');
    } else {
        return view('Centaur::auth.login');
    }
}]);

// Main Page
Route::get('/help', ['as' => 'help', 'uses' => 'Controller@viewHelp']);


// Authorization
Route::get('/login', ['as' => 'auth.login.form', 'uses' => 'Auth\SessionController@getLogin']);
Route::post('/login', ['as' => 'auth.login.attempt', 'uses' => 'Auth\SessionController@postLogin']);
Route::get('/logout', ['as' => 'auth.logout', 'uses' => 'Auth\SessionController@getLogout']);

// Registration
Route::get('register', ['as' => 'auth.register.form', 'uses' => 'Auth\RegistrationController@getRegister']);
Route::post('register', ['as' => 'auth.register.attempt', 'uses' => 'Auth\RegistrationController@postRegister']);

// Activation
Route::get('activate/{code}', ['as' => 'auth.activation.attempt', 'uses' => 'Auth\RegistrationController@getActivate']);
Route::get('resend', ['as' => 'auth.activation.request', 'uses' => 'Auth\RegistrationController@getResend']);
Route::post('resend', ['as' => 'auth.activation.resend', 'uses' => 'Auth\RegistrationController@postResend']);

// Password Reset
Route::get('password/reset/{code}', ['as' => 'auth.password.reset.form', 'uses' => 'Auth\PasswordController@getReset']);
Route::post('password/reset/{code}', ['as' => 'auth.password.reset.attempt', 'uses' => 'Auth\PasswordController@postReset']);
Route::get('password/reset', ['as' => 'auth.password.request.form', 'uses' => 'Auth\PasswordController@getRequest']);
Route::post('password/reset', ['as' => 'auth.password.request.attempt', 'uses' => 'Auth\PasswordController@postRequest']);

// Users
Route::resource('users', 'UserController');

// Roles
Route::resource('roles', 'RoleController');


Route::get('/', ['as' => 'home', 'uses' => function() {
    if (Sentinel::check()) {
        return view('dashboard');
    } else {
        return view('Centaur::auth.login');
    }
}]);

/** Category Controller **/
Route::group(
    ['middleware' => ['web','sentinel.auth'], 'prefix' => 'category'],
    function () {
        Route::get('list/', 'CategoryController@viewList')->name('CategoryController.viewList');
        Route::post('get-category-list', 'CategoryController@getList')->name('CategoryController.getList');

    }
);

/** Category Controller **/
Route::group(
    ['middleware' => ['web','sentinel.auth'], 'prefix' => 'aspect'],
    function () {
        Route::get('list/{categoryId?}', 'AspectController@viewList')->name('AspectController.viewList');
        Route::post('get-aspect-list', 'AspectController@getList')->name('AspectController.getList');
    }
);

/** Product Controller **/
Route::group(
    ['middleware' => ['web','sentinel.auth'], 'prefix' => 'product'],
    function () {
        Route::get('list/{categoryId?}', 'ProductController@viewList')->name('ProductController.viewList');
        Route::get('upload-panel', 'ProductController@viewUploadPanel')->name('ProductController.viewUploadPanel');
        Route::get('product/{productId}', 'ProductController@viewProduct')->name('ProductController.viewProduct');
        Route::get('gold-summary-recommendation/{productId}', 'ProductController@viewGoldSummaryRecommendation')
            ->name('ProductController.viewGoldSummaryRecommendation');

        Route::post('get-product-list', 'ProductController@getList')->name('ProductController.getList');
        Route::post('upload', 'ProductController@upload')->name('ProductController.upload');
        Route::post('get-gold-summary-recommendation', 'ProductController@getGoldSummaryRecommendation')
            ->name('ProductController.getGoldSummaryRecommendation');

    }
);

/** Comment Controller **/
Route::group(
    ['middleware' => ['web','sentinel.auth'], 'prefix' => 'comment'],
    function () {
        Route::get('list/{productId}', 'CommentController@viewList')->name('CommentController.viewList');
        Route::get('upload-panel', 'CommentController@index')->name('CommentController.index');
        Route::post('get-comment-list', 'CommentController@getList')->name('CommentController.getList');
        Route::post('upload', 'CommentController@upload')->name('CommentController.upload');
        Route::post('update-comments', 'CommentController@updateComments')->name('CommentController.updateComments');

    }
);

/** Sentence Controller **/
Route::group(
    ['middleware' => ['web','sentinel.auth'], 'prefix' => 'sentence'],
    function () {
        Route::get('list/{commentId}', 'SentenceController@viewList')
            ->name('SentenceController.viewList');
        Route::post('calculate-entropy', 'SentenceController@calculateEntropy')
            ->name('SentenceController.calculateEntropy');
        Route::post('generate-word2vec-input', 'SentenceController@generateWord2VecInput')
            ->name('SentenceController.generateWord2VecInput');
        Route::post('store-sentences', 'SentenceController@storeSentences')
            ->name('SentenceController.storeSentences');
        Route::post('compute-sentence-entropy', 'SentenceController@computeSentencesEntropy')
            ->name('SentenceController.computeSentencesEntropy');
        Route::post('get-sentence-list', 'SentenceController@getList')
            ->name('SentenceController.getList');
        Route::post('update-gold-sentences', 'SentenceController@updateGoldSentences')
            ->name('SentenceController.updateGoldSentences');
        Route::post('update-sentence-gold-status', 'SentenceController@updateSentenceGoldStatus')
            ->name('SentenceController.updateSentenceGoldStatus');
    }
);

/** Word Controller **/
Route::group(
    ['middleware' => ['web','sentinel.auth'], 'prefix' => 'word'],
    function () {
        Route::get('word-manager-panel', 'WordController@viewWordManagerPanel')->name('WordController.viewWordManagerPanel');
        Route::get('list{categoryId?}', 'WordController@viewList')->name('WordController.viewList');

        Route::post('store-words', 'WordController@storeWords')->name('WordController.storeWords');
        Route::post('compute-word-entropy', 'WordController@computeWordsEntropy')->name('WordController.computeWordsEntropy');
        Route::post('clean-words', 'WordController@cleanWords')->name('WordController.cleanWords');
        Route::post('get-list', 'WordController@getList')->name('WordController.getList');

    }
);

/** statistics **/
Route::group(
    ['middleware' => ['web','sentinel.access:users.create'], 'prefix' => 'statistics'],
    function () {
        Route::get('evaluation_results', 'StatisticsController@viewResults')->name('StatisticsController.viewResults');

    }
);

