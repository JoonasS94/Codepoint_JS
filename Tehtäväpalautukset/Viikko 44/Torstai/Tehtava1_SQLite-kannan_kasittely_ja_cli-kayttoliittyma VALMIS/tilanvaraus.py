import sqlite3
from datetime import datetime

DB_NAME = "tilanvaraus.db"

#Create database if don't exist.
def create_database():
    """Create new database and tables if they do not exist."""
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()

        #Create reservations table.
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS Varaukset (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nimi INTEGER NOT NULL,
                varauspaiva TEXT NOT NULL,
                tila INTEGER NOT NULL,
                FOREIGN KEY (nimi) REFERENCES Varaajat(id) ON DELETE CASCADE,
                FOREIGN KEY (tila) REFERENCES Tilat(id)
            )
        ''')
        #Create reservators table.
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS Varaajat (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                varaajan_nimi TEXT NOT NULL
            )
        ''')
        #Create reservation places table.
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS Tilat (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tilan_nimi TEXT NOT NULL
            )
        ''')
        conn.commit()

def add_reservator(nimi):
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        cursor.execute('INSERT INTO Varaajat (varaajan_nimi) VALUES (?)', (nimi,))
        conn.commit()
        print("Varaaja lisätty.")

def add_reservation(reservator_id, paiva, tila_id):
    """Lisätään uusi varaus tietokantaan."""
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        #Check if reservator with that ID number exist.
        cursor.execute('SELECT COUNT(1) FROM Varaajat WHERE id = ?', (reservator_id,))
        if cursor.fetchone()[0] == 0:
            print(f"Virhe: Varaajaa ID:llä {reservator_id} ei löytynyt.")
            return
        #Check if reservation place with that ID number exist.
        cursor.execute('SELECT COUNT(1) FROM Tilat WHERE id = ?', (tila_id,))
        if cursor.fetchone()[0] == 0:
            print(f"Virhe: Tilaa ID:llä {tila_id} ei löytynyt.")
            return
        try:
            #Check if date in accepted form.
            parsed_date = datetime.strptime(paiva, "%d-%m-%Y")
            cursor.execute('INSERT INTO Varaukset (nimi, varauspaiva, tila) VALUES (?, ?, ?)', 
                           (reservator_id, parsed_date.strftime("%Y-%m-%d"), tila_id))
            conn.commit()
            print("Varaus lisätty.")
        except ValueError:
            print("Virhe: Päivämäärä on väärässä muodossa. Käytä muotoa DD-MM-YYYY.")

def remove_reservation(reservation_id):
    """Poista varaus tietokannasta ID:n perusteella."""
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        #Check if reservation to be removed with that ID exist.
        cursor.execute('SELECT COUNT(1) FROM Varaukset WHERE id = ?', (reservation_id,))
        if cursor.fetchone()[0] == 0:
            print(f"Virhe: Varausta ID:llä {reservation_id} ei löytynyt.")
            return
        
        cursor.execute('DELETE FROM Varaukset WHERE id = ?', (reservation_id,))
        conn.commit()
        print(f"Varaus ID:llä {reservation_id} poistettu.")

#Show on top of page all reservation(s).
def show_reservations():
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        cursor.execute('''
            SELECT Varaukset.id, 
                   IFNULL(Varaajat.varaajan_nimi, 'Tuntematon varaaja') AS varaajan_nimi, 
                   Varaukset.varauspaiva, 
                   IFNULL(Tilat.tilan_nimi, 'Tuntematon tila') AS tilan_nimi
            FROM Varaukset
            LEFT JOIN Varaajat ON Varaukset.nimi = Varaajat.id
            LEFT JOIN Tilat ON Varaukset.tila = Tilat.id
        ''')
        varaukset = cursor.fetchall()
        print("\nVaraukset: ")
        if varaukset:
            for varaus in varaukset:
                print(f"ID: {varaus[0]}, Varaaja: {varaus[1]}, Varauspäivä: {varaus[2]}, Tila: {varaus[3]}")
        else:
            print("Ei varauksia.")

#Show on top of page all reservators.
def show_all_reservators():
    """Näytä kaikki varaajat."""
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM Varaajat')
        varaajat = cursor.fetchall()
        print("\nVaraajat: ")
        if varaajat:
            for varaaja in varaajat:
                print(f"ID: {varaaja[0]}, Varaajan nimi: {varaaja[1]}")
        else:
            print("Ei varaajia.")

def add_space(nimi):
    """Lisää tila tietokantaan."""
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        cursor.execute('INSERT INTO Tilat (tilan_nimi) VALUES (?)', (nimi,))
        conn.commit()
        print("Tila lisätty.")

#Show on top of page all places available for reservation.
def show_all_spaces():
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM Tilat')
        tilat = cursor.fetchall()
        print("\nVarattavat tilat:")
        if tilat:
            for tila in tilat:
                print(f"ID: {tila[0]}, Tilan nimi: {tila[1]}")
        else:
            print("Ei tiloja.")

def remove_space(space_id):
    """Poista tila tietokannasta ID:n perusteella, jos sillä ei ole varauksia."""
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        #Check if reservations done with place that's about to be removed.
        #If exist, prevent removal of place. 
        cursor.execute('SELECT COUNT(1) FROM Varaukset WHERE tila = ?', (space_id,))
        if cursor.fetchone()[0] > 0:
            print(f"Virhe: Tilaa ID:llä {space_id} ei voi poistaa, koska sillä on voimassa olevia varauksia.")
            return

        cursor.execute('DELETE FROM Tilat WHERE id = ?', (space_id,))
        conn.commit()
        print("Tila poistettu.")

def remove_reservator(reservator_id):
    """Poista varaaja tietokannasta, jos ei ole varauksia."""
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        #Check if reservations done by that reservator that's about to be removed.
        #If exist, prevent removal of reservator. 
        cursor.execute('SELECT COUNT(1) FROM Varaukset WHERE nimi = ?', (reservator_id,))
        if cursor.fetchone()[0] > 0:
            print(f"Virhe: Varaajaa ID:llä {reservator_id} ei voi poistaa, koska sillä on voimassa olevia varauksia.")
            return
        
        cursor.execute('SELECT COUNT(1) FROM Varaajat WHERE id = ?', (reservator_id,))
        if cursor.fetchone()[0] == 0:
            print(f"Virhe: Varaajaa ID:llä {reservator_id} ei löytynyt.")
            return
        
        try:
            cursor.execute('DELETE FROM Varaajat WHERE id = ?', (reservator_id,))
            conn.commit()
            print(f"Varaaja ID:llä {reservator_id} poistettu.")
        except Exception as e:
            print(f"Virhe poistettaessa varaajaa: {e}")

def main():
    create_database()
    #Text field showing user the available options.
    while True:
        print("\nTILANVARAUSJÄRJESTELMÄ\n")
        
        show_reservations()
        show_all_reservators()
        show_all_spaces()
        
        print("\n1. Lisää varaus")
        print("2. Poista varaus")
        print("3. Lisää varaaja")
        print("4. Poista varaaja")
        print("5. Lisää varattava tila")
        print("6. Poista varattava tila")
        print("7. Sammuta")
        
        valinta = input("Valitse toiminto (1-7): ")

        if valinta == '1':
            reservator_id = int(input("Syötä varaajan ID: "))
            paiva = input("Syötä päivämäärä (DD-MM-YYYY): ")
            tila_id = int(input("Syötä tilan ID: "))
            add_reservation(reservator_id, paiva, tila_id)
        elif valinta == '2':
            try:
                reservation_id = int(input("Syötä poistettavan varauksen ID: "))
                remove_reservation(reservation_id)
            except ValueError:
                print("Virheellinen ID.")
        elif valinta == '3':
            nimi = input("Syötä varaajan nimi: ")
            add_reservator(nimi)
        elif valinta == '4':
            try:
                reservator_id = int(input("Syötä poistettavan varaajan ID: "))
                remove_reservator(reservator_id)
            except ValueError:
                print("Virheellinen ID.")
        elif valinta == '5':
            nimi = input("Syötä tilan nimi: ")
            add_space(nimi)
        elif valinta == '6':
            try:
                space_id = int(input("Syötä poistettavan tilan ID: "))
                remove_space(space_id)
            except ValueError:
                print("Virheellinen ID.")
        elif valinta == '7':
            print("Ohjelma lopetetaan.")
            break
        else:
            print("Virheellinen valinta. Yritä uudelleen.")

if __name__ == "__main__":
    main()