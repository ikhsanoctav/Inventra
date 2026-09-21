import os

route_str = '''
use App\\Http\\Controllers\\SearchController;
Route::get('/search', [SearchController::class, 'index'])->name('search.index')->middleware('auth');
'''
with open(r'd:\PROYEKAN\inventory_Logistik\routes\web.php', 'a', encoding='utf-8') as f:
    f.write(route_str)
