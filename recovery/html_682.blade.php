<!DOCTYPE html>' in line:
            # We found a chunk that has HTML.
            # Usually tool output contains the file contents.
            # Let's just find the first <?php or <!DOCTYPE html> to the end of </html>
            try:
                data = json.loads(line)
            except:
                try:
                    data = json.load(json.loads(json.dumps(ast.literal_eval(repr(line)))))
                except:
                    continue
            
            content_str = json.dumps(data)
            
            # Find the index of '<!DOCTYPE html>'
            start = content_str.find('<!DOCTYPE html>')
            end = content_str.rfind('</html>