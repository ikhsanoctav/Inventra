<!DOCTYPE html>' in line or '@extends' in line:
            try:
                data = json.loads(line)
            except:
                continue
            
            content_str = json.dumps(data)
            
            if '<!DOCTYPE html>' in content_str and '</html>' in content_str:
                start = content_str.find('<!DOCTYPE html>')
                end = content_str.rfind('</html>