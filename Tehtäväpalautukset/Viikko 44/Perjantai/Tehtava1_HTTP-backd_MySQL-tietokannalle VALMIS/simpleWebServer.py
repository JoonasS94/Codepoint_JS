#Bring HTTPServer and BaseHTTPRequestHandler classes from http.server module.
from http.server import HTTPServer, BaseHTTPRequestHandler

class SimpleHTTPRequestHandler(BaseHTTPRequestHandler):

    def do_GET(self):
        if self.path == '/':
            #200 = status message "OK".
            self.send_response(200)
            self.send_header('Content-type', 'text/plain; charset=utf-8')
            self.end_headers()
            
            try:
                #Read SQL-file.
                with open('127_0_0_1.sql', 'r', encoding='utf-8') as file:
                    lines = file.readlines()
                    
                    #Define the rows that want to keep.
                    lines_to_include = []
                    current_table_name = ""

                    for line in lines:
                        #Searth for tables.
                        if line.startswith("CREATE TABLE"):
                            #Get table's name.
                            current_table_name = line.split('`')[1]
                            #Keep the table's name but get rid of everything else.
                            lines_to_include.append(f"{current_table_name}\n")
                        
                        #Keep only rows that contain values.
                        if line.strip().startswith('('):
                            #Keep the table's values but get rid of everything else.
                            lines_to_include.append(line.strip() + '\n')  # Lisätään vain arvot

                    #Print the filtered content.
                    filtered_content = ''.join(lines_to_include)
                    self.wfile.write(filtered_content.encode('utf-8'))
            except FileNotFoundError:
                self.send_response(404)
                self.end_headers()
                self.wfile.write(b'File not found.')
            except Exception as e:
                self.send_response(500)
                self.end_headers()
                self.wfile.write(f'Internal server error: {str(e)}'.encode('utf-8'))
        else:
            self.send_response(404)
            self.end_headers()
            self.wfile.write(b'Not found.')

#You can access the site with web-browser using url http://localhost:8000/ .
httpd = HTTPServer(('', 8000), SimpleHTTPRequestHandler)
print("Server running on http://localhost:8000/")
#Keep website running until closed.
httpd.serve_forever()