import os
import re

files_to_check = []
for root, dirs, files in os.walk('resources/views'):
    for file in files:
        if file.endswith('.blade.php') and 'app.blade.php' not in file:
            files_to_check.append(os.path.join(root, file))

for file in files_to_check:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Remove @if(session('success')) block
    success_pattern = re.compile(r'\s*<!-- Alert Success -->\s*@if\(session\(\'success\'\)\).*?@endif', re.DOTALL)
    content = success_pattern.sub('', content)
    
    # Sometimes there is no <!-- Alert Success --> comment
    success_pattern2 = re.compile(r'\s*@if\(session\(\'success\'\)\).*?@endif', re.DOTALL)
    # We must be careful not to remove too much. So let's write a safer regex.
    success_pattern_safe = re.compile(r'\s*@if\(\s*session\(\s*\'success\'\s*\)\s*\).*?@endif\s*', re.DOTALL)
    # Actually, it's safer to only do it if the file was found by our grep
    if "session('success')" in content:
        # Instead of aggressive regex, let's just do a simple replacement for the known pattern
        content = re.sub(r'\s*<!-- Alert Success -->\s*@if\(session\(\'success\'\)\)[\s\S]*?@endif\s*', '\n', content)
        content = re.sub(r'\s*@if\(session\(\'success\'\)\)[\s\S]*?@endif\s*', '\n', content)
        content = re.sub(r'\s*@if\(session\(\'error\'\)\)[\s\S]*?@endif\s*', '\n', content)
        
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Removed duplicate session alerts in {file}")
