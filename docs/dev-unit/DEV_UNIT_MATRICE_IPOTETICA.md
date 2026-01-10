### Matrice DEV UNIT – L'Altramusica (calcolata da ODS con granularità Footility)

> Nota: valori calcolati analizzando i file ODS del cliente e applicando la metodologia DEV UNIT di Footility con granularità completa.

**Metodologia Footility applicata:**
- **DEV_DB_campi**: ogni campo database = 1 DEV UNIT (contati dalle colonne ODS)
- **DEV_DB_relazioni**: ogni foreign key/relazione = 1 DEV UNIT
- **DEV_LOGIC_CRUD**: ogni action controller (index, create, store, show, edit, update, destroy) = 1 DEV UNIT
- **DEV_LOGIC_workflow**: ogni stato/transizione workflow = 1 DEV UNIT
- **DEV_UI_form**: ogni campo input nel form = 1 DEV UNIT
- **DEV_UI_lista**: ogni colonna nella lista = 1 DEV UNIT, ogni filtro = 1 DEV UNIT
- **DEV_UI_stampa**: ogni campo nel PDF = 1 DEV UNIT, ogni tipo di stampa = 1 DEV UNIT

**Dettaglio completo:** Vedi `DEV_UNIT_CALCOLO_DETTAGLIATO.md`

| Macro attività        | Task / Funzione specifica                          | DEV_DB_campi | DEV_DB_relazioni | DEV_LOGIC_CRUD | DEV_LOGIC_workflow | DEV_UI_form | DEV_UI_lista | DEV_UI_stampa |
|-----------------------|----------------------------------------------------|--------------|------------------|----------------|--------------------|------------|--------------|---------------|
| Anagrafiche           | Anagrafica studenti                               | 25           | 6                | 7              | 0                  | 25         | 20           | 0             |
| Anagrafiche           | Anagrafica genitori/tutori                        | 18           | 2                | 7              | 0                  | 18         | 16           | 0             |
| Anagrafiche           | Anagrafica docenti                                | 23           | 3                | 7              | 0                  | 23         | 18           | 0             |
| Anagrafiche           | Gestione anno scolastico                          | 6            | 5                | 7              | 0                  | 6          | 8            | 0             |
| Iscrizioni            | Corsi e iscrizioni                                | 15           | 4                | 7              | 1                  | 15         | 17           | 0             |
| Iscrizioni            | Calendario lezioni (giornate attive/sospensioni)  | 5            | 2                | 7              | 1                  | 5          | 10           | 0             |
| Primo contatto        | Gestione primo contatto / prospect                | 9            | 2                | 7              | 0                  | 9          | 11           | 0             |
| Storici               | Contratti storici                                 | 0            | 0                | 1              | 0                  | 0          | 15           | 0             |
| Storici               | Fatture storiche                                  | 0            | 0                | 1              | 0                  | 0          | 15           | 0             |
| Storici               | Pagamenti storici                                 | 0            | 0                | 1              | 0                  | 0          | 15           | 0             |
| Extra (base)          | Attività extra (orchestra, coro) – base           | 12           | 3                | 7              | 0                  | 12         | 14           | 0             |
| Presenze (base)       | Registro presenze minimo                          | 6            | 3                | 7              | 0                  | 6          | 13           | 0             |
| Note operative        | Note su comunicazioni manuali                     | 6            | 2                | 0              | 0                  | 6          | 0            | 0             |
| Utenze                | Gestione utenze (account, ruoli)                  | 8            | 2                | 7              | 1                  | 8          | 11           | 0             |
| Utenze                | Controllo accessi e autorizzazioni                | 4            | 2                | 0              | 2                  | 0          | 0            | 0             |
| Contratti             | Contratti da iscrizioni                           | 13           | 4                | 7              | 4                  | 13         | 16           | 48            |
| Contratti             | Modelli standard di contratto                     | 6            | 1                | 0              | 1                  | 6          | 0            | 0             |
| Contratti             | Generazione PDF contratti                         | 0            | 0                | 0              | 1                  | 0          | 0            | 48            |
| Fatturazione          | Gestione fatturazione/pagamenti/recupero crediti  | 18           | 5                | 7              | 3                  | 18         | 20           | 13            |
| Fatturazione          | Piani di pagamento flessibili                     | 7            | 2                | 0              | 2                  | 7          | 0            | 0             |
| Fatturazione          | Prime automazioni di sollecito                    | 5            | 2                | 0              | 2                  | 0          | 9            | 0             |
| Fornitori             | Anagrafica fornitori                              | 10           | 2                | 7              | 0                  | 10         | 12           | 0             |
| Registro avanzato     | Registro elettronico evoluto                      | 10           | 4                | 7              | 2                  | 10         | 16           | 0             |
| Registro avanzato     | Gestione recuperi lezioni                         | 8            | 3                | 0              | 2                  | 8          | 13           | 0             |
| Registro avanzato     | Conto orario docenti completo                     | 12           | 3                | 0              | 2                  | 0          | 15           | 13            |
| Registro avanzato     | Gestione supplenze                                | 8            | 3                | 0              | 2                  | 0          | 13           | 0             |
| Extra evolute         | Attività extra evolute (orchestra/coro)           | 12           | 4                | 7              | 2                  | 12         | 14           | 0             |
| Didattica avanzata    | Gestione esami                                    | 10           | 3                | 7              | 1                  | 10         | 14           | 0             |
| Didattica avanzata    | Generazione attestati di frequenza                | 5            | 1                | 0              | 1                  | 0          | 0            | 9             |
| Comunicazioni evolute | Comunicazione Mail / SMS evoluta                  | 9            | 3                | 0              | 2                  | 9          | 13           | 0             |

**TOTALE GENERALE: 280 + 68 + 98 + 32 + 223 + 309 + 131 = 1141 DEV UNIT**

