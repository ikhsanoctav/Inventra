import json
import base64

with open(r'C:\Users\AERO-PC\.gemini\antigravity-ide\brain\e9c6ab4b-0d7a-4796-b077-f54458e583f1\.system_generated\logs\transcript_full.jsonl', 'r', encoding='utf-8') as f:
    for line in reversed(list(f)):
        try:
            data = json.loads(line)
            if data.get('type') == 'USER_INPUT' and 'ubah logo jadi ini' in data.get('content', ''):
                # The image might be in a different part of the JSON, maybe in an array?
                # Let's search the whole string for the base64 pattern instead of assuming structure
                pass
        except Exception:
            pass

import re
with open(r'C:\Users\AERO-PC\.gemini\antigravity-ide\brain\e9c6ab4b-0d7a-4796-b077-f54458e583f1\.system_generated\logs\transcript_full.jsonl', 'r', encoding='utf-8') as f:
    content = f.read()
    matches = re.findall(r'"url":\s*"data:image/png;base64,([^"]+)"', content)
    if matches:
        b64_data = matches[-1] # Get the last one just in case
        with open('public/images/logo.png', 'wb') as img_f:
            img_f.write(base64.b64decode(b64_data))
        print("Logo saved successfully!")
    else:
        print("No image found in log.")
