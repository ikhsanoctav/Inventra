import os
import re

files_to_check = []
for root, dirs, files in os.walk('resources/views'):
    for file in files:
        if file.endswith('.blade.php'):
            files_to_check.append(os.path.join(root, file))

for file in files_to_check:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if '<!-- Modal Panel -->' in content or '<!-- Modal panel -->' in content:
        # We want to add 'relative z-10' to the class attribute of the Modal Panel div
        new_content = re.sub(r'(<!-- Modal [Pp]anel -->\s*<div[^>]+class=")([^"]+)(")', 
                             lambda m: m.group(1) + m.group(2) + ' relative z-10' + m.group(3) if 'relative z-10' not in m.group(2) else m.group(0), 
                             content)
        if new_content != content:
            with open(file, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f'Fixed modal stacking context in {file}')
