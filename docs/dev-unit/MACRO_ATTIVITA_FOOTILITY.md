# Macro Attività per Footility - Progetto L'Altramusica

**Progetto Footility:** https://footility.test/projects/13  
**Data:** Dicembre 2024

---

## Priorità Funzionalità (da Transcript)

### URGENTE - Fine Agosto 2026
- Anagrafiche complete
- Primo Contatto
- Iscrizioni
- Contratti
- Fatturazione

### URGENTE - Primi Settembre 2026
- Proposta Oraria

### URGENTE - Fine Settembre/Ottobre 2026
- Registro Elettronico
- Presenze
- Conto Orario Insegnanti
- Orchestra/Attività Extra

### RIPARTIBILE 2026
- Magazzino
- Integrazioni avanzate (Cassetto Fiscale, Flusso Cassa)
- Reportistica avanzata
- Esami
- Preiscrizioni (serve a fine anno)

---

## Macro Attività da Inserire in Footility

### Macro 1: Infrastruttura Base (76h)
**Scadenza:** Fine Agosto 2026  
**Priorità:** CRITICA

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Setup progetto e ambiente | 16 | - |
| Gestione utenze | 24 | Setup |
| Controllo accessi | 16 | Gestione utenze |
| Sicurezza GDPR | 20 | Controllo accessi |

**Totale:** 76h

---

### Macro 2: Anagrafiche (132h)
**Scadenza:** Fine Agosto 2026  
**Priorità:** CRITICA  
**Dipende da:** Macro 1

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Gestione Esercizio/Anno Scolastico | 20 | Infrastruttura |
| Gestione Studenti | 32 | Esercizio |
| Gestione Genitori/Tutore | 20 | Studenti |
| Anagrafica Fornitori e Clienti | 16 | - |
| Gestione Docenti | 24 | - |

**Totale:** 132h

---

### Macro 3: Primo Contatto e Iscrizioni (84h)
**Scadenza:** Fine Agosto 2026  
**Priorità:** CRITICA  
**Dipende da:** Macro 2

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Primo Contatto | 24 | Anagrafiche |
| Calendario Lezioni | 20 | - |
| Iscrizione e Corsi | 40 | Primo Contatto, Calendario |

**Totale:** 84h

---

### Macro 4: Contratti e Fatturazione (144h)
**Scadenza:** Fine Agosto 2026  
**Priorità:** CRITICA  
**Dipende da:** Macro 3

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Gestione Contratti | 40 | Iscrizioni |
| Gestione Fatturazione | 48 | Contratti |
| Gestione Pagamenti | 32 | Fatturazione |
| Recupero Crediti | 24 | Pagamenti |

**Totale:** 144h

---

### Macro 5: Proposta Oraria (32h)
**Scadenza:** Primi Settembre 2026  
**Priorità:** CRITICA  
**Dipende da:** Macro 3

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Sistema composizione orari | 32 | Iscrizioni, Calendario |

**Totale:** 32h

---

### Macro 6: Didattica e Registro (132h)
**Scadenza:** Fine Settembre 2026  
**Priorità:** ALTA  
**Dipende da:** Macro 5

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Registro Elettronico | 32 | Proposta Oraria |
| Gestione Presenze | 20 | Registro |
| Conto Orario Insegnanti | 40 | Presenze |
| Gestione Supplenti | 24 | Registro |
| Gestione Aule | 16 | - |

**Totale:** 132h

---

### Macro 7: Attività Extra e Comunicazioni (88h)
**Scadenza:** Fine Ottobre 2026  
**Priorità:** ALTA  
**Dipende da:** Macro 6

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Attività Extra (Orchestra/Coro) | 32 | Registro |
| Generazione Attestati | 16 | Presenze |
| Comunicazione e Privacy | 40 | - |

**Totale:** 88h

---

### Macro 8: Integrazioni e Reportistica (116h)
**Scadenza:** Dopo Ottobre 2026  
**Priorità:** MEDIA  
**Dipende da:** Macro 4

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Integrazione Cassetto Fiscale | 32 | Fatturazione |
| Flusso di Cassa | 24 | Pagamenti |
| Pianificazione Annuale e Reportistica | 40 | - |
| Preiscrizioni | 20 | - |

**Totale:** 116h

---

### Macro 9: Magazzino e Altro (48h)
**Scadenza:** Dopo Ottobre 2026  
**Priorità:** MEDIA

| Attività | Ore | Dipendenze |
|----------|-----|------------|
| Gestione Accessori e Strumenti | 32 | - |
| Gestione Esami | 16 | - |

**Totale:** 48h

---

---

## Riepilogo Macro Attività

| Macro | Ore | Scadenza | Priorità |
|-------|-----|----------|----------|
| 1. Infrastruttura Base | 76 | Fine Agosto 2026 | CRITICA |
| 2. Anagrafiche | 132 | Fine Agosto 2026 | CRITICA |
| 3. Primo Contatto e Iscrizioni | 84 | Fine Agosto 2026 | CRITICA |
| 4. Contratti e Fatturazione | 144 | Fine Agosto 2026 | CRITICA |
| 5. Proposta Oraria | 32 | Primi Settembre 2026 | CRITICA |
| 6. Didattica e Registro | 132 | Fine Settembre 2026 | ALTA |
| 7. Attività Extra e Comunicazioni | 88 | Fine Ottobre 2026 | ALTA |
| 8. Integrazioni e Reportistica | 116 | Dopo Ottobre 2026 | MEDIA |
| 9. Magazzino e Altro | 48 | Dopo Ottobre 2026 | MEDIA |
| **TOTALE** | **852h** | | |

---

## Formato API Footility

**Endpoint:** `POST /api/v1/activities`

**Esempio per Macro 1:**
```json
{
  "project_id": 13,
  "title": "Infrastruttura Base",
  "description": "Setup progetto, gestione utenze, controllo accessi, sicurezza GDPR",
  "estimated_duration": 4560,
  "status": "pending"
}
```

**Nota:** `estimated_duration` è in minuti (76h × 60 = 4560 minuti)

---

## Materializzazione Progetto

**Endpoint:** `POST /planning/materialize`

Dopo aver inserito tutte le attività, eseguire la materializzazione per calcolare i tempi di consegna basati su:
- Ore disponibili/giorno
- Dipendenze tra attività
- Scadenze

**Risultato:** Timeline con date di inizio/fine per ogni attività

---

## Prossimi Passi

1. ✅ Identificare priorità (FATTO)
2. ⏳ Inserire macro attività in Footility (progetto 13)
3. ⏳ Materializzare progetto per vedere tempi
4. ⏳ Analizzare timeline e validare scadenze

