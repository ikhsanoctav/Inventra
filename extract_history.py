import json

transcript_path = r'C:\Users\AERO-PC\.gemini\antigravity-ide\brain\c6c33493-493d-4c6c-be9d-450aae9fa1ea\.system_generated\logs\transcript_full.jsonl'
output_path = r'C:\Users\AERO-PC\.gemini\antigravity-ide\brain\c6c33493-493d-4c6c-be9d-450aae9fa1ea\scratch\user_history.md'

with open(transcript_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

with open(output_path, 'w', encoding='utf-8') as f:
    f.write("# Riwayat Percakapan User\n\n")
    for line in lines:
        try:
            data = json.loads(line)
            if data.get('type') == 'USER_INPUT':
                content = data.get('content', '')
                f.write(f"## Step Index: {data.get('step_index')}\n\n")
                f.write(f"```text\n{content}\n```\n\n")
                f.write("---\n\n")
        except:
            pass

print("User history written to user_history.md")
