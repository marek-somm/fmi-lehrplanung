import sqlite3
import json
import time

conn = sqlite3.connect('database.sqlite')
c = conn.cursor()

# clean tables

c.execute('''DELETE FROM events''')
c.execute('''DELETE FROM event_module''')
c.execute('''DELETE FROM event_user''')

rythm = {
    "keine Übernahme": 0,
    "Jedes Semester": 1,
    "Jedes 2. Semester": 2
}

files = ['Veranstaltungen_SoSe15', 'Veranstaltungen_WiSe15', 'Veranstaltungen_SoSe16', 'Veranstaltungen_WiSe16', 'Veranstaltungen_SoSe17', 'Veranstaltungen_WiSe17',
         'Veranstaltungen_SoSe18', 'Veranstaltungen_WiSe18', 'Veranstaltungen_SoSe19', 'Veranstaltungen_WiSe19', 'Veranstaltungen_SoSe20', 'Veranstaltungen_WiSe20', 'Veranstaltungen_SoSe21']


def getTime():
   return time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())


prID = 0
for filename in files:
   with open("data/Lehrveranstaltungen/"+filename+".json", encoding='utf8') as file:
      data = json.load(file)

   for elem in data:
      # events
      elem = data[elem]
      veranstaltungsnr = elem['Veranstaltungsnummer']
      art = elem['Veranstaltungsart']
      rhythmus = rythm[elem['Rhythmus']] if 'Rhythmus' in elem else None

      semester = filename.replace("Veranstaltungen_", "")
      semester += "1" if semester.__contains__("WiSe") else "0"
      semester = semester.replace("SoSe", "20").replace("WiSe", "20")
      titel = elem['Titel']
      frID = elem['FriedolinID']
      aktiv = elem['aktiv']
      sws = elem['SWS'] if 'SWS' in elem else None

      inhalt = elem['Inhalt'] if 'Inhalt' in elem else {}
      kommentar = inhalt['Kommentar'] if 'Kommentar' in inhalt else None
      literatur = inhalt['Literatur'] if 'Literatur' in inhalt else None
      bemerkung = inhalt['Bemerkung'] if 'Bemerkung' in inhalt else None
      zielgruppe = inhalt['Zielgruppe'] if 'Zielgruppe' in inhalt else None
      lerninhalte = inhalt['Lerninhalte'] if 'Lerninhalte' in inhalt else None
      leistungsnachweis = inhalt['Leistungsnachweis'] if 'Leistungsnachweis' in inhalt else None
      time_now = getTime()

      c.execute('''INSERT INTO events
         (vnr,semester,title,active,sws,type,targets,rotation,changed,created_at,updated_at)
         VALUES (?,?,?,?,?,?,?,?,?,?,?)''',
                [veranstaltungsnr, semester, titel, aktiv, sws,
                 art, zielgruppe, rhythmus, 0, time_now, time_now]
                )

      # event_user
      personen = elem['Personen'] if 'Personen' in elem else {}
      verantwortlich = personen['verantwortlich'] if 'verantwortlich' in personen else [
      ]
      begleitend = personen['begleitend'] if 'begleitend' in personen else []
      organisatorisch = personen['organisatorisch'] if 'organisatorisch' in personen else [
      ]
      rollen = {
          "verantwortlich": verantwortlich,
          "begleitend": begleitend,
          "organisatorisch": organisatorisch
      }

      for rolle in rollen:
         personen = rollen[rolle]

         for person in personen:
            split = person.split(",")
            forename = split[1].strip() if len(
                split) > 1 and split[1].strip() != "" else None
            surname = split[0].strip() if split[0].strip() != "" else None
            time_now = getTime()

            user_id = c.execute("SELECT id FROM users WHERE surname is ?", [
                                surname]).fetchall()

            if len(user_id) > 1:
               user_id = c.execute("SELECT id FROM users WHERE forename is ? and surname is ?", [
                                   forename, surname]).fetchall()

            user_id = user_id[0][0] if len(user_id) > 0 else None

            if(user_id is not None):
               c.execute('''
                  INSERT INTO event_user
                  (event_id, user_id, created_at, updated_at) 
                  VALUES(
                     (SELECT id FROM events WHERE vnr is ? and semester is ?), 
                     ?,
                     ?,?
                  )''',
                  [veranstaltungsnr, semester, user_id, time_now, time_now])

      # event_module

      prüfungen = elem['Prüfungen'] if 'Prüfungen' in elem else []

      for prüfung in prüfungen:
         modulcode = prüfung['Modul']
         pnr = prüfung['PNr']
         venr = prüfung['VENr']
         vetitel = prüfung['VETitel']
         time_now = getTime()

         module_id = c.execute('SELECT id FROM modules WHERE modulecode is ?', [modulcode]).fetchall()
         module_id = module_id[0][0] if len(module_id) > 0 else None

         if(module_id is not None):
            exists = c.execute('''SELECT id FROM event_module
               WHERE event_id = (SELECT id FROM events WHERE vnr is ? and semester is ? and pnr is ?) and module_id = ?''',
               [veranstaltungsnr, semester, pnr, module_id]).fetchall()
            if(len(exists) == 0):
               c.execute('''
                  INSERT INTO event_module
                  (event_id, module_id, pnr, description, title, changed, created_at, updated_at)
                  VALUES (
                     (SELECT id FROM events WHERE vnr is ? and semester is ?), 
                     ?,?,?,?,?,?,?)''',
                  [veranstaltungsnr, semester, module_id, pnr, None, vetitel, 0, time_now, time_now])

         # PRUEFUNG_LEHRVERANSTALTUNG

         # c.execute('''INSERT INTO BRIDGE_Lehrveranstaltung_Pruefung
         #   VALUES(?,?)''',
         #   [lehrvID, prID]
         # )

 #      prüfungen = elem['Prüfungen'] if 'Prüfungen' in elem else []
 #
 #      for prüfung in prüfungen:
 #         modulcode = prüfung['Modul']
 #         pnr = prüfung['PNr']
 #
 #         modulID = c.execute('''SELECT id FROM modul WHERE modulcode=? AND pnr=?''', [modulcode, pnr]).fetchall()
 #
 #         modulID = modulID[0][0] if len(modulID) > 0 else None
 #
 #         if(modulID is None):
 #            if modulcode not in wrongModule:
 #               wrongModule[modulcode] = 1
 #            else:
 #               wrongModule[modulcode] += 1
 #            if modulcode == "BB3.MLS4":
 #               print(modulcode + "\t - " + str(pnr) + "\t" + str(modulID))


conn.commit()
conn.close()
