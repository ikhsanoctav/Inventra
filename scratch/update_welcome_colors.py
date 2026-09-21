import re

file_path = r'd:\PROYEKAN\inventory_Logistik\resources\views\welcome.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

new_colors = '''"colors": {
            "error-container": "#ffdad6",
            "surface-container-highest": "#e2e8f0",
            "on-secondary-container": "#334155",
            "on-error": "#ffffff",
            "inverse-on-surface": "#f1f5f9",
            "outline-variant": "#cbd5e1",
            "on-primary-container": "#eff6ff",
            "on-error-container": "#93000a",
            "inverse-surface": "#1e293b",
            "on-secondary-fixed-variant": "#475569",
            "primary-container": "#2563eb",
            "surface-tint": "#1d4ed8",
            "inverse-primary": "#93c5fd",
            "secondary-fixed-dim": "#cbd5e1",
            "outline": "#64748b",
            "on-tertiary-fixed-variant": "#0369a1",
            "tertiary": "#0284c7",
            "secondary-container": "#e2e8f0",
            "on-background": "#0f172a",
            "surface-container-high": "#f1f5f9",
            "on-primary-fixed-variant": "#1e40af",
            "on-surface": "#0f172a",
            "surface-variant": "#e2e8f0",
            "secondary": "#64748b",
            "surface-container-low": "#f8fafc",
            "background": "#f9f9ff",
            "on-primary-fixed": "#1e3a8a",
            "secondary-fixed": "#f1f5f9",
            "on-tertiary-container": "#f0f9ff",
            "on-tertiary-fixed": "#0c4a6e",
            "tertiary-container": "#bae6fd",
            "tertiary-fixed": "#e0f2fe",
            "on-secondary": "#ffffff",
            "on-secondary-fixed": "#0f172a",
            "on-tertiary": "#ffffff",
            "surface-container-lowest": "#ffffff",
            "surface": "#ffffff",
            "on-surface-variant": "#475569",
            "error": "#ba1a1a",
            "surface-container": "#f8fafc",
            "on-primary": "#ffffff",
            "primary-fixed-dim": "#93c5fd",
            "primary": "#1d4ed8",
            "primary-fixed": "#dbeafe",
            "surface-dim": "#cbd5e1",
            "tertiary-fixed-dim": "#7dd3fc",
            "surface-bright": "#ffffff"
          }'''

content = re.sub(r'"colors":\s*\{[^\}]+\}', new_colors, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Welcome page colors updated to Blue')
