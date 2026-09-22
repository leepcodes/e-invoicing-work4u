    <?php

    use Inertia\Inertia;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\LandingController;
    use App\Http\Controllers\Auth\RegisterController;
    use App\Http\Controllers\Auth\LoginController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\InvoiceController;
    use App\Http\Controllers\BuyerController;
    use App\Http\Controllers\Import\InvoiceImportController;
    use App\Http\Controllers\SellerController;
    use App\Http\Controllers\MailerController;
    use App\Http\Controllers\ItemController;


    Route::get('/', fn() => Inertia::render('landing/Index'))->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('/register', [RegisterController::class, 'show'])->name('register');
        Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('/login', [LoginController::class, 'show'])->name('login');
    });

    Route::middleware(['auth', 'verified'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //Invoice
        Route::prefix('invoice')->name('invoice.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');

            //Import csv
            Route::get('/import', [InvoiceImportController::class, 'create'])->name('import');
            Route::post('/import', [InvoiceImportController::class, 'store'])->name('import.store');
            Route::get('/import/{importJob}', [InvoiceImportController::class, 'show'])->name('import.show');

            Route::get('/create', [InvoiceController::class, 'create'])->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
            Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');

            // PDF
            Route::get('/{invoice}/pdf', [InvoiceController::class, 'viewPdf'])->name('pdf');
            Route::get('/{invoice}/pdf/download', [InvoiceController::class, 'downloadPdf'])->name('pdf.download');

            // Mail
            Route::post('/{invoice}/mail', [MailerController::class, 'sendInvoice'])->name('mail');

            //Invoice JSON
            Route::get('/{invoice}/json', [InvoiceController::class, 'json'])->name('json');
        });

        //Seller
        Route::prefix('seller')->name('seller.')->group(function () {
            Route::get('/', [SellerController::class, 'index'])->name('index');
            Route::get('/edit', [SellerController::class, 'edit'])->name('edit');
            Route::put('/', [SellerController::class, 'update'])->name('update');
        });

        //Buyer
        Route::prefix('buyer')->name('buyer.')->group(function () {
            Route::get('/', [BuyerController::class, 'index'])->name('index');
            Route::get('/create', [BuyerController::class, 'create'])->name('create');
            Route::post('/', [BuyerController::class, 'store'])->name('store');
            Route::get('/{buyer}', [BuyerController::class, 'show'])->name('show');
            Route::get('/{buyer}/edit', [BuyerController::class, 'edit'])->name('edit');
            Route::put('/{buyer}', [BuyerController::class, 'update'])->name('update');
            Route::delete('/{buyer}', [BuyerController::class, 'destroy'])->name('destroy');
        });

        // Mailer
        Route::get('/mailer', [MailerController::class, 'index'])->name('mailer.index');
        Route::post('/mailer/send', [MailerController::class, 'send'])->name('mailer.send');
        Route::get('/mailer/jobs', [MailerController::class, 'jobs'])->name('mailer.jobs');
        Route::get('/mailer/{emailJob}', [MailerController::class, 'show'])->name('mailer.show');

        //Items
        Route::resource('item', ItemController::class);

        //CSV Template
        Route::get('/invoice/import/template/csv', function () {return response()->download(
        storage_path('app/templates/invoice_import_template.csv'),
        'invoice_import_template.csv');})->name('invoice.import.template.csv');

        //XLSX Template
        Route::get('/invoice-import/template', [InvoiceImportController::class, 'downloadTemplate'])
        ->name('invoice-import.template');

    });

    require __DIR__.'/settings.php';
