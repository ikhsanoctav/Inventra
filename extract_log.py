import json
import os
import ast

log_file = r'C:\Users\AERO-PC\.gemini\antigravity-ide\brain\c6c33493-493d-4c6c-be9d-450aae9fa1ea\.system_generated\logs\transcript_full.jsonl'
output_dir = r'd:\PROYEKAN\inventory_Logistik\recovery'
os.makedirs(output_dir, exist_ok=True)

recovered = {}

with open(log_file, 'r', encoding='utf-8') as f:
    for line in f:
        if '<!DOCTYPE html>' in line or '@extends' in line or 'div class=' in line:
            try:
                data = json.loads(line)
            except:
                continue
            
            content_str = json.dumps(data)
            
            if '<!DOCTYPE html>' in content_str and '</html>' in content_str:
                start = content_str.find('<!DOCTYPE html>')
                end = content_str.rfind('</html>') + 7
                raw_html = content_str[start:end]
                raw_html = raw_html.encode('utf-8').decode('unicode_escape')
                raw_html = raw_html.replace('\\n', '\n').replace('\\r', '\r').replace('\\"', '"')
                if 'INVENTRA - Sistem Manajemen Logistik Pintar' not in raw_html:
                    hash_id = str(len(raw_html))
                    recovered[f'html_{hash_id}.blade.php'] = raw_html
                        
            elif '@extends' in content_str and '@endsection' in content_str:
                start = content_str.find('@extends')
                end = content_str.rfind('@endsection') + 11
                raw_html = content_str[start:end]
                raw_html = raw_html.encode('utf-8').decode('unicode_escape')
                raw_html = raw_html.replace('\\n', '\n').replace('\\r', '\r').replace('\\"', '"')
                hash_id = str(len(raw_html))
                if hash_id not in ['803', '775', '808']: # skip dummies length
                    recovered[f'extends_{hash_id}.blade.php'] = raw_html
                    
            elif '<!-- Sidebar -->' in content_str and '</aside>' in content_str:
                start = content_str.find('<!-- Sidebar -->')
                end = content_str.rfind('</aside>') + 8
                raw_html = content_str[start:end]
                raw_html = raw_html.encode('utf-8').decode('unicode_escape')
                raw_html = raw_html.replace('\\n', '\n').replace('\\r', '\r').replace('\\"', '"')
                recovered[f'sidebar_{len(raw_html)}.blade.php'] = raw_html
                
            elif '<!-- Topbar -->' in content_str and '</header>' in content_str:
                start = content_str.find('<!-- Topbar -->')
                end = content_str.rfind('</header>') + 9
                raw_html = content_str[start:end]
                raw_html = raw_html.encode('utf-8').decode('unicode_escape')
                raw_html = raw_html.replace('\\n', '\n').replace('\\r', '\r').replace('\\"', '"')
                recovered[f'topbar_{len(raw_html)}.blade.php'] = raw_html

for name, content in recovered.items():
    out_path = os.path.join(output_dir, name)
    with open(out_path, 'w', encoding='utf-8') as out:
        out.write(content)
    print('Recovered:', name, 'Length:', len(content))
