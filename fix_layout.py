import os
import re

files = [
    'resources/views/master/categories/index.blade.php',
    'resources/views/master/items/index.blade.php',
    'resources/views/master/suppliers/index.blade.php',
    'resources/views/master/units/index.blade.php'
]

# Specifically fix the corrupted suppliers file first
suppliers_path = 'resources/views/master/suppliers/index.blade.php'
if os.path.exists(suppliers_path):
    with open(suppliers_path, 'r') as f:
        content = f.read()

    target = '                            </div>\n                        </td>\n        <div x-show="showModal"'
    if target in content:
        replacement = '''                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Placeholder -->
        <div class="px-6 py-4 border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#6B7280]">
            <span>Menampilkan {{ $suppliers->count() }} data</span>
            <div class="flex items-center gap-1">
                <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50" disabled><span class="material-symbols-outlined text-[18px]">chevron_left</span></button>
                <button class="p-1 rounded hover:bg-gray-100 disabled:opacity-50" disabled><span class="material-symbols-outlined text-[18px]">chevron_right</span></button>
            </div>
        </div>
    </div>

    <!-- Add Data Modal Overlay -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background backdrop -->
        <div x-show="showModal"'''
        content = content.replace(target, replacement)
        with open(suppliers_path, 'w') as f:
            f.write(content)

# Now apply layout fixes to all files
for file in files:
    if not os.path.exists(file):
        continue
    with open(file, 'r') as f:
        content = f.read()
    
    # Fix the header overlap
    content = re.sub(r'<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">', 
                     '<div class="flex flex-wrap items-center justify-between gap-4">\n        <div class="min-w-[200px] flex-1">', content)
    content = re.sub(r'<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">', 
                     '<div class="flex flex-wrap items-center justify-between gap-4">\n        <div class="min-w-[200px] flex-1">', content)
    
    # Fix the search bar wrapper
    content = re.sub(r'<div class="flex items-center gap-3">', '<div class="flex flex-wrap sm:flex-nowrap items-center gap-3 shrink-0">', content)
    
    # Fix table headers to prevent squishing on mobile (add whitespace-nowrap)
    content = re.sub(r'<th scope="col" class="px-6 py-4">', '<th scope="col" class="px-6 py-4 whitespace-nowrap">', content)
    
    # Fix 'Menampilkan 0 data' to count
    var_name = file.split('/')[-2]
    content = re.sub(r'<span>Menampilkan 0 data</span>', f'<span>Menampilkan {{{{ ${var_name}->count() }}}} data</span>', content)
    
    with open(file, 'w') as f:
        f.write(content)
print('Fixed layouts and pagination placeholders!')
