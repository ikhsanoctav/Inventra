import os
import re

files_to_check = []
for root, dirs, files in os.walk('resources/views'):
    for file in files:
        if file.endswith('.blade.php'):
            files_to_check.append(os.path.join(root, file))

files_fixed = 0

for file in files_to_check:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check if we see the pattern "number: " at the beginning of the lines
    if re.search(r'^\d+:\s', content, re.MULTILINE):
        # We need to strip the line numbers.
        # But we must be careful not to strip legitimate code.
        # Typically these are '1: ', '2: ', etc.
        # Let's count how many lines start with this.
        matches = len(re.findall(r'^\d+:\s', content, re.MULTILINE))
        
        # If there are a significant number of matches (like > 10), it's definitely the artifact bug
        if matches > 10:
            print(f'Fixing line numbers in {file} ({matches} lines)')
            new_content = re.sub(r'^\d+:\s', '', content, flags=re.MULTILINE)
            
            with open(file, 'w', encoding='utf-8') as f:
                f.write(new_content)
            files_fixed += 1

print(f'Finished. Fixed {files_fixed} files.')
