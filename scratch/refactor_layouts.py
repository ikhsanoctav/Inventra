import re

# 1. Update guest.blade.php
guest_path = r'd:\PROYEKAN\inventory_Logistik\resources\views\layouts\guest.blade.php'
with open(guest_path, 'r', encoding='utf-8') as f:
    guest_content = f.read()

unified_tailwind = '''<script>
        if (!document.getElementById('tailwind-config')) {
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            body: ['Inter', 'sans-serif'],
                            headline: ['Plus Jakarta Sans', 'sans-serif'],
                        },
                        colors: {
                            "primary-container": "#2563eb",
                            "primary": "#1d4ed8",
                            "on-primary": "#ffffff",
                            "surface": "#f9f9ff",
                            "surface-container-lowest": "#ffffff",
                            "surface-container-low": "#f0f8ff",
                            "surface-container": "#e0f2fe",
                            "surface-container-high": "#bae6fd",
                            "surface-container-highest": "#7dd3fc",
                            "on-surface": "#111827",
                            "secondary": "#6b7280",
                            "outline": "#e5e7eb",
                        },
                        boxShadow: {
                            'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                            'glow': '0 0 15px rgba(37, 99, 235, 0.3)',
                            'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .material-symbols-fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin 1s linear infinite;
        }
    </style>'''

guest_content = re.sub(r'<script>.*?</style>', unified_tailwind, guest_content, flags=re.DOTALL)
with open(guest_path, 'w', encoding='utf-8') as f:
    f.write(guest_content)


# 2. Refactor login.blade.php
login_path = r'd:\PROYEKAN\inventory_Logistik\resources\views\auth\login.blade.php'
with open(login_path, 'r', encoding='utf-8') as f:
    login_content = f.read()

# Extract body class, main content, and scripts
body_class_match = re.search(r'<body class="([^"]+)"', login_content)
body_class = body_class_match.group(1) if body_class_match else ''

main_content = re.search(r'<div class="min-h-screen w-full flex flex-col lg:flex-row">.*</div>\s*<!-- Interactive JavaScript', login_content, re.DOTALL)
if main_content:
    main_html = main_content.group(0).replace('<!-- Interactive JavaScript', '').strip()
else:
    main_html = ''

scripts_match = re.search(r'<script>(.*?)</script>\s*</body>', login_content, re.DOTALL)
scripts = scripts_match.group(1) if scripts_match else ''
# Need to filter out the tailwind config from scripts if it was mistakenly caught
if 'tailwind.config' in scripts:
    scripts_match = re.finditer(r'<script>(.*?)</script>', login_content, re.DOTALL)
    for match in scripts_match:
        if 'tailwind.config' not in match.group(1) and 'isPasswordVisible' in match.group(1):
            scripts = match.group(1)

new_login = f'''@extends('layouts.guest')
@section('title', 'INVENTRA — Masuk ke Sistem Inventaris')
@section('body_class', '{body_class}')

@section('content')
{main_html}
@endsection

@section('scripts')
<script>
{scripts}
</script>
@endsection
'''
with open(login_path, 'w', encoding='utf-8') as f:
    f.write(new_login)

print("login.blade.php successfully refactored to use layouts.guest")
