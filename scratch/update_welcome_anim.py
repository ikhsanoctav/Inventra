import re

file_path = r'd:\PROYEKAN\inventory_Logistik\resources\views\welcome.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add AOS & Alpine
if 'aos@2.3.1' not in content:
    content = content.replace('</title>', '</title>\n<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">\n<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>')
    content = content.replace('</body></html>', '<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>\n<script>\n  AOS.init({ once: true, duration: 800, offset: 100 });\n</script>\n</body></html>')

# 2. Update style block for animations and gradients
style_block = '''<style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }
    .custom-shadow-soft {
      box-shadow: 0 10px 40px -10px rgba(37, 99, 235, 0.1);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-shadow-soft:hover {
      box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.2);
      transform: translateY(-4px);
    }
    .custom-shadow-elevated {
      box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.25);
    }
    
    .gradient-text {
      background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .float-anim {
      animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
      100% { transform: translateY(0px); }
    }
    
    .glass-header {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    }
  </style>'''
content = re.sub(r'<style>.*?</style>', style_block, content, flags=re.DOTALL)

# 3. Enhance Header with Alpine glass effect
content = content.replace('<header class="sticky top-0 z-40 w-full bg-surface-container-lowest/95 backdrop-blur-md border-b border-surface-container-highest">',
'<header x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)" :class="scrolled ? \'glass-header shadow-sm border-surface-container-highest/50\' : \'bg-surface-container-lowest border-transparent\'" class="fixed top-0 z-50 w-full transition-all duration-300">')
content = content.replace('bg-surface-container-lowest/95 backdrop-blur-md border-b', '')

# 4. Add AOS data attributes
# Hero
content = content.replace('<div class="lg:col-span-6 flex flex-col items-start">', '<div class="lg:col-span-6 flex flex-col items-start" data-aos="fade-right">')
content = content.replace('<div class="lg:col-span-6 relative">', '<div class="lg:col-span-6 relative float-anim" data-aos="fade-left" data-aos-delay="200">')
content = content.replace('<span class="text-primary-container">Terintegrasi</span>', '<span class="gradient-text">Terintegrasi</span>')

# Stat Cards
content = content.replace('<!-- Stat Card 1 -->\n<div', '<!-- Stat Card 1 -->\n<div data-aos="fade-up" data-aos-delay="100"')
content = content.replace('<!-- Stat Card 2 -->\n<div', '<!-- Stat Card 2 -->\n<div data-aos="fade-up" data-aos-delay="200"')
content = content.replace('<!-- Stat Card 3 -->\n<div', '<!-- Stat Card 3 -->\n<div data-aos="fade-up" data-aos-delay="300"')
content = content.replace('<!-- Stat Card 4 -->\n<div', '<!-- Stat Card 4 -->\n<div data-aos="fade-up" data-aos-delay="400"')

# Section titles
content = content.replace('<div class="text-center max-w-2xl mx-auto mb-16">', '<div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">')

# Grid features
for i in range(1, 9):
    content = content.replace(f'<!-- Card {i} -->\n<div', f'<!-- Card {i} -->\n<div data-aos="zoom-in-up" data-aos-delay="{i*50}"')

# Process Steps
content = content.replace('<!-- Step 1 -->\n<div', '<!-- Step 1 -->\n<div data-aos="fade-right" data-aos-delay="100"')
content = content.replace('<!-- Step 2 -->\n<div', '<!-- Step 2 -->\n<div data-aos="fade-up" data-aos-delay="200"')
content = content.replace('<!-- Step 3 -->\n<div', '<!-- Step 3 -->\n<div data-aos="fade-left" data-aos-delay="300"')

# Save
with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Premium animations and styling added.')
