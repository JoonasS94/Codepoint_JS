from http.server import HTTPServer, BaseHTTPRequestHandler

class SimpleHTTPRequestHandler(BaseHTTPRequestHandler):

    def do_GET(self):
        if self.path == '/':
            self.send_response(200)
            self.send_header('Content-type', 'text/plain; charset=utf-8')
            self.end_headers()
            
            # Luetaan SQL-tiedosto ja valitaan vain halutut rivit
            try:
                with open('127_0_0_1.sql', 'r', encoding='utf-8') as file:
                    lines = file.readlines()
                    
                    # Määritellään rivit, jotka halutaan pitää
                    lines_to_include = []
                    current_table_name = ""

                    for line in lines:
                        # Etsitään taulun nimi ja lisätään se
                        if line.startswith("CREATE TABLE"):
                            # Haetaan taulun nimi
                            current_table_name = line.split('`')[1]  # Oletetaan, että taulun nimi on ensimmäisessä backtickissä
                            lines_to_include.append(f"{current_table_name}\n")  # Lisätään vain taulun nimi
                        
                        # Etsitään vain rivit, jotka sisältävät arvot
                        if line.strip().startswith('('):
                            # Lisätään vain arvot suoraan
                            lines_to_include.append(line.strip() + '\n')  # Lisätään vain arvot

                    # Muodostetaan suodatettu sisältö
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

httpd = HTTPServer(('', 8000), SimpleHTTPRequestHandler)
print("Server running on http://localhost:8000/")
httpd.serve_forever()