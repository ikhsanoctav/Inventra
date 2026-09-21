import os
import re

ROUTES = {
    'master/categories/index.blade.php': 'master.categories',
    'master/branches/index.blade.php': 'master.branches',
    'master/suppliers/index.blade.php': 'master.suppliers',
    'master/units/index.blade.php': 'master.units',
    'system/users/index.blade.php': 'system.users',
    'system/audit/index.blade.php': 'system.audit',
    'system/rbl/index.blade.php': 'system.rbl',
}

CONTROLLERS = {
    'CategoryController.php': 'Category',
    'BranchController.php': 'Branch',
    'SupplierController.php': 'Supplier',
    'UnitController.php': 'Unit',
    'UserController.php': 'User',
}

BASE_PATH = r'd:\PROYEKAN\inventory_Logistik'

def process_view(rel_path, route_name):
    abs_path = os.path.join(BASE_PATH, 'resources', 'views', rel_path.replace('/', '\\'))
    if not os.path.exists(abs_path):
        return False
    
    with open(abs_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if 'id="table-container"' in content:
        return False
    
    # 1. Table container
    content = re.sub(
        r'<!-- Data Table -->\s*<div class="bg-white rounded-2xl border',
        r'<!-- Data Table -->\n    <div id="table-container" class="bg-white rounded-2xl border',
        content
    )

    # 2. Convert search input to form
    form_html = f'''<form method="GET" action="{{{{ route('{route_name}') }}}}" 
                  hx-get="{{{{ route('{route_name}') }}}}"
                  hx-target="#table-container"
                  hx-select="#table-container"
                  hx-swap="outerHTML"
                  hx-trigger="input changed delay:500ms from:input[name='search']"
                  class="relative flex-1 w-full sm:w-auto"
                  x-data="{{ loading: false }}"
                  @htmx:before-request.camel="loading = true"
                  @htmx:after-request.camel="loading = false">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF] text-[18px]">search</span>
                <input type="text" name="search" value="{{{{ request('search') }}}}" placeholder="Cari data..." class="w-full sm:w-64 h-10 pl-9 pr-4 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#111827] focus:outline-none focus:border-[#4F46E5] focus:ring-2 focus:ring-[#4F46E5]/20 transition-all shadow-sm">
                
                <div x-show="loading" style="display: none;" class="absolute -top-3 right-4 bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-[10px] font-bold flex items-center gap-1 shadow-sm">
                    <span class="material-symbols-outlined text-[12px] animate-spin">refresh</span> Loading...
                </div>
            </form>'''
            
    content = re.sub(
        r'<div class="relative">\s*<span class="material-symbols-outlined.*?search</span>\s*<input type="text" placeholder="Cari data.*?</div>',
        form_html,
        content,
        flags=re.DOTALL
    )

    # 3. Replace pagination
    var_match = re.search(r'Menampilkan \{\{ \$([a-zA-Z_]+)->count\(\) \}\} data', content)
    if var_match:
        var_name = var_match.group(1)
        paginator_html = f'''<div class="px-6 py-4 border-t border-[#E5E7EB] text-xs text-[#6B7280]">
            {{{{ ${var_name}->appends(request()->query())->links('vendor.pagination.tailwind') }}}}
        </div>'''
        content = re.sub(
            r'<!-- Pagination Placeholder -->.*?</div>\s*</div>\s*</div>',
            paginator_html + '\n    </div>\n',
            content,
            flags=re.DOTALL
        )
    else:
        # Some views might not have the count
        var_match = re.search(r'Menampilkan.*?\{\{ \$([a-zA-Z_]+)', content)
        if var_match:
            var_name = var_match.group(1)
            paginator_html = f'''<div class="px-6 py-4 border-t border-[#E5E7EB] text-xs text-[#6B7280]">
                {{{{ ${var_name}->appends(request()->query())->links('vendor.pagination.tailwind') }}}}
            </div>'''
            content = re.sub(
                r'<!-- Pagination Placeholder -->.*?</div>\s*</div>\s*</div>',
                paginator_html + '\n    </div>\n',
                content,
                flags=re.DOTALL
            )
    
    with open(abs_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated view: {rel_path}")
    return True

def process_controller(filename, model_name):
    abs_path = os.path.join(BASE_PATH, 'app', 'Http', 'Controllers', filename)
    if not os.path.exists(abs_path):
        return False
        
    with open(abs_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    if '->paginate(' in content:
        return False
        
    index_match = re.search(r'public function index\((.*?)\)\s*\{(.*?)\}', content, re.DOTALL)
    if index_match:
        params = index_match.group(1)
        body = index_match.group(2)
        
        if 'Request $request' not in params:
            new_params = 'Request $request' if not params.strip() else 'Request $request, ' + params
        else:
            new_params = params
            
        if '->get()' in body:
            new_body = body.replace('->get()', "->when($request->search, function ($query) use ($request) {\n            $query->where('name', 'like', '%' . $request->search . '%');\n        })->paginate(10)")
        elif '::all()' in body:
            new_body = body.replace('::all()', "::when($request->search, function ($query) use ($request) {\n            $query->where('name', 'like', '%' . $request->search . '%');\n        })->paginate(10)")
        elif '::latest()->get()' in body:
            new_body = body.replace('::latest()->get()', "::when($request->search, function ($query) use ($request) {\n            $query->where('name', 'like', '%' . $request->search . '%');\n        })->latest()->paginate(10)")
        else:
            new_body = body
            
        content = content.replace(f'public function index({params})\n    {{{body}}}', f'public function index({new_params})\n    {{{new_body}}}')
        
    with open(abs_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated controller: {filename}")
    return True

for rel_path, route_name in ROUTES.items():
    process_view(rel_path, route_name)

for filename, model_name in CONTROLLERS.items():
    process_controller(filename, model_name)
