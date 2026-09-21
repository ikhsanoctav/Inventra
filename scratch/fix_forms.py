import os

files = [
    'resources/views/master/categories/index.blade.php',
    'resources/views/master/units/index.blade.php',
    'resources/views/master/suppliers/index.blade.php',
    'resources/views/master/branches/index.blade.php'
]

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # The script erroneously injected "\n    </form>" instead of newlines.
    content = content.replace('\\n    </form>', '\n    </form>')
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Fixed literal newlines.")
