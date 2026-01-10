# Riepilogo Attività Fase 1 per Footility

**Data:** Dicembre 2024  
**Progetto:** L'Altramusica  
**Fase:** FASE 1 - Traduzione 1:1 ODS → DB Normalizzato

---

## Struttura Dati

**File dati:** `scripts/footility_activities_data.php`  
**Script inserimento:** `scripts/insert_footility_activities_with_dev_units.php`

### Struttura Attività

Ogni attività contiene:
- **title**: Nome attività (macro processo)
- **description**: Descrizione completa
- **dev_units**: Breakdown DEV UNIT (db_campi, db_relazioni, crud, workflow, ui_form, ui_lista, ui_stampa, total)
- **tasks**: Array di task (dettagli implementativi)
- **er_model**: Modello E/R (entity, attributes, relationships)

Ogni task contiene:
- **title**: Nome task
- **description**: Descrizione task
- **dev_units**: DEV UNIT del task
- **subtasks**: Array di subtask (dettagli operativi)

Ogni subtask contiene:
- **title**: Nome subtask
- **dev_units**: DEV UNIT del subtask

---

## Riepilogo Attività Fase 1

### GRUPPO 1: ANAGRAFICHE BASE

1. **Studenti - CRUD Completo** - 146 DEV UNIT
2. **Genitori/Tutori - CRUD Completo** - 70 DEV UNIT
3. **Docenti - CRUD Completo** - 86 DEV UNIT
4. **Anno Scolastico - CRUD Completo** - 39 DEV UNIT

**Totale Gruppo 1:** 341 DEV UNIT

---

### GRUPPO 2: DISPONIBILITÀ E ORARI

5. **Disponibilità Studenti - CRUD Completo** - 46 DEV UNIT
6. **Disponibilità Docenti - CRUD Completo** - 46 DEV UNIT
7. **Calendario Lezioni - CRUD Completo** - 50 DEV UNIT
8. **Sospensioni Calendario - CRUD Completo** - 36 DEV UNIT

**Totale Gruppo 2:** 178 DEV UNIT

---

### GRUPPO 3: CORSI E ISCRIZIONI

9. **Tipi Corso - CRUD Completo** - 40 DEV UNIT
10. **Corsi - CRUD Completo** - 66 DEV UNIT
11. **Iscrizioni - CRUD Completo** - 55 DEV UNIT

**Totale Gruppo 3:** 161 DEV UNIT

---

### GRUPPO 4: CONTRATTI E FATTURAZIONE

12. **Contratti - CRUD Completo** - 86 DEV UNIT
13. **Fatture - CRUD Completo** - 64 DEV UNIT
14. **Fatture Accessori - CRUD Completo** - 53 DEV UNIT
15. **Pagamenti - CRUD Completo** - 48 DEV UNIT

**Totale Gruppo 4:** 251 DEV UNIT

---

### GRUPPO 5: DIDATTICA E VALUTAZIONE

16. **Livelli Studenti - CRUD Completo** - 41 DEV UNIT
17. **Esami - CRUD Completo** - 54 DEV UNIT

**Totale Gruppo 5:** 95 DEV UNIT

---

### GRUPPO 6: ATTIVITÀ EXTRA

18. **Orchestra/Coro - CRUD Completo** - 47 DEV UNIT

**Totale Gruppo 6:** 47 DEV UNIT

---

### GRUPPO 7: MAGAZZINO

19. **Strumenti - CRUD Completo** - 50 DEV UNIT
20. **Noleggi Strumenti - CRUD Completo** - 59 DEV UNIT
21. **Accessori - CRUD Completo** - 53 DEV UNIT
22. **Libri - CRUD Completo** - 54 DEV UNIT
23. **Contratti Docenti - CRUD Completo** - 59 DEV UNIT
24. **Conto Orario Docenti - CRUD Completo** - 50 DEV UNIT

**Totale Gruppo 7:** 325 DEV UNIT

---

## TOTALE FASE 1

**Totale Attività:** 24  
**Totale DEV UNIT:** ~1.398 DEV UNIT

**Breakdown DEV UNIT:**
- DB_campi: ~400
- DB_relazioni: ~80
- CRUD: ~168 (24 attività × 7)
- Workflow: ~30
- UI_form: ~300
- UI_lista: ~420
- UI_stampa: 0

---

## Conversione DEV UNIT → Ore

**Stima base:** 1 DEV UNIT = 12 minuti (0.2 ore)

**Totale ore stimate:** 1.398 × 0.2 = **~280 ore**

**Range realistico:** 250-300 ore

---

## Modelli E/R Inclusi

Ogni attività principale include il modello E/R con:
- **entity**: Nome entità principale
- **attributes**: Elenco attributi
- **relationships**: Relazioni (belongsTo, belongsToMany, hasMany)

---

## Prossimi Passi

1. ⏳ Eseguire script inserimento in Footility
2. ⏳ Verificare struttura task/subtask in Footility
3. ⏳ Affinare DEV UNIT se necessario
4. ⏳ Footility bilancerà con COSMIC per generare preventivi

---

**File creati:**
- `scripts/footility_activities_data.php` - Dati completi 24 attività
- `scripts/insert_footility_activities_with_dev_units.php` - Script inserimento
- `docs/RIEPILOGO_FOOTILITY_FASE1.md` - Questo documento
