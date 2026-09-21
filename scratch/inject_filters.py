import os
import re

# 1. Update UomController
uom_ctrl_path = 'app/Http/Controllers/UnitController.php'
with open(uom_ctrl_path, 'r', encoding='utf-8') as f:
    uom_ctrl = f.read()

uom_ctrl = re.sub(
    r'public function index\(Request \$request\)\s*\{\s*\$uoms = Uom::query\(\)\s*->when\(\$request->search, function \(\$query, \$search\) \{\s*\$query->where\(\'name\', \'like\', "%\{\$search\}%"\)\s*->orWhere\(\'symbol\', \'like\', "%\{\$search\}%"\);\s*\}\)\s*->latest\(\)\s*->paginate\(10\);',
    '''public function index(Request $request)
    {
        $uoms = Uom::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('symbol', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($request->get('per_page', 10));''',
    uom_ctrl
)

with open(uom_ctrl_path, 'w', encoding='utf-8') as f:
    f.write(uom_ctrl)

# 2. Update SupplierController
supp_ctrl_path = 'app/Http/Controllers/SupplierController.php'
with open(supp_ctrl_path, 'r', encoding='utf-8') as f:
    supp_ctrl = f.read()

supp_ctrl = re.sub(
    r'public function index\(Request \$request\)\s*\{\s*\$suppliers = Supplier::query\(\)\s*->when\(\$request->search, function \(\$query, \$search\) \{\s*\$query->where\(\'name\', \'like\', "%\{\$search\}%"\)\s*->orWhere\(\'contact_person\', \'like\', "%\{\$search\}%"\);\s*\}\)\s*->latest\(\)\s*->paginate\(10\);',
    '''public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate($request->get('per_page', 10));''',
    supp_ctrl
)

with open(supp_ctrl_path, 'w', encoding='utf-8') as f:
    f.write(supp_ctrl)


# 3. Update BranchController
branch_ctrl_path = 'app/Http/Controllers/BranchController.php'
with open(branch_ctrl_path, 'r', encoding='utf-8') as f:
    branch_ctrl = f.read()

branch_ctrl = re.sub(
    r'public function index\(Request \$request\)\s*\{\s*\$branches = Branch::query\(\)\s*->when\(\$request->search, function \(\$query, \$search\) \{\s*\$query->where\(\'name\', \'like\', "%\{\$search\}%"\)\s*->orWhere\(\'code\', \'like\', "%\{\$search\}%"\);\s*\}\)\s*->latest\(\)\s*->paginate\(10\);',
    '''public function index(Request $request)
    {
        $branches = Branch::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->latest()
            ->paginate($request->get('per_page', 10));''',
    branch_ctrl
)
with open(branch_ctrl_path, 'w', encoding='utf-8') as f:
    f.write(branch_ctrl)


# HTML updates
def add_per_page(html):
    # Find hx-trigger and replace
    html = re.sub(r'hx-trigger="input changed delay:500ms from:input\[name=\\\'search\\\'\]"', 
                  r'hx-trigger="input changed delay:500ms from:input[name=\'search\'], change from:select"', html)
    
    per_page_html = """
        <div class="w-full md:w-32 mt-4 md:mt-0 md:ml-4 flex-shrink-0">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tampilkan</label>
            <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
            </select>
        </div>"""
    
    # insert before closing </form>
    html = re.sub(r'(</form>)', r'' + per_page_html + r'\\n    \1', html)
    return html


# Category HTML
cat_html_path = 'resources/views/master/categories/index.blade.php'
with open(cat_html_path, 'r', encoding='utf-8') as f:
    cat_html = f.read()
cat_html = add_per_page(cat_html)
with open(cat_html_path, 'w', encoding='utf-8') as f:
    f.write(cat_html)


# UoM HTML
uom_html_path = 'resources/views/master/units/index.blade.php'
with open(uom_html_path, 'r', encoding='utf-8') as f:
    uom_html = f.read()
uom_html = add_per_page(uom_html)
with open(uom_html_path, 'w', encoding='utf-8') as f:
    f.write(uom_html)


# Supplier HTML (Add status and per_page)
supp_html_path = 'resources/views/master/suppliers/index.blade.php'
with open(supp_html_path, 'r', encoding='utf-8') as f:
    supp_html = f.read()

supp_html = re.sub(r'hx-trigger="input changed delay:500ms from:input\[name=\\\'search\\\'\]"', 
              r'hx-trigger="input changed delay:500ms from:input[name=\'search\'], change from:select"', supp_html)

supp_filters = """
        <div class="w-full md:w-48 mt-4 md:mt-0 md:ml-4 flex-shrink-0">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Supplier</label>
            <select name="status" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        <div class="w-full md:w-32 mt-4 md:mt-0 md:ml-4 flex-shrink-0">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tampilkan</label>
            <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
            </select>
        </div>"""
supp_html = re.sub(r'(</form>)', r'' + supp_filters + r'\\n    \1', supp_html)

with open(supp_html_path, 'w', encoding='utf-8') as f:
    f.write(supp_html)

# Branch HTML (Add status and per_page)
branch_html_path = 'resources/views/master/branches/index.blade.php'
with open(branch_html_path, 'r', encoding='utf-8') as f:
    branch_html = f.read()

branch_html = re.sub(r'hx-trigger="input changed delay:500ms from:input\[name=\\\'search\\\'\]"', 
              r'hx-trigger="input changed delay:500ms from:input[name=\'search\'], change from:select"', branch_html)

branch_filters = """
        <div class="w-full md:w-48 mt-4 md:mt-0 md:ml-4 flex-shrink-0">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Cabang</label>
            <select name="status" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        <div class="w-full md:w-32 mt-4 md:mt-0 md:ml-4 flex-shrink-0">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tampilkan</label>
            <select name="per_page" class="w-full h-10 px-3 text-sm bg-slate-50 border border-[#E5E7EB] rounded-lg focus:outline-none focus:bg-white focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all cursor-pointer">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
            </select>
        </div>"""
branch_html = re.sub(r'(</form>)', r'' + branch_filters + r'\\n    \1', branch_html)

with open(branch_html_path, 'w', encoding='utf-8') as f:
    f.write(branch_html)

print("Filters added successfully.")
