<!DOCTYPE html>' or 'extends('
        if ('welcome.blade.php' in line or 'dashboard' in line or 'index.blade.php' in line) and ('<!DOCTYPE html>' in line or '@extends' in line):
            try:
                data = json.loads(line)
            except:
                continue
            
            content_str = json.dumps(data)
            
            # Very crude extraction: find '<!DOCTYPE html>' to '</html>' or '@extends' to '@endsection'
            if '<!DOCTYPE html>' in content_str and '</html>' in content_str:
                start = content_str.find('<!DOCTYPE html>')
                end = content_str.rfind('</html>