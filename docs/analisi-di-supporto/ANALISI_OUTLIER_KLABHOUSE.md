# Analisi Outlier Klabhouse

**Data Analisi:** Dicembre 2024  
**Obiettivo:** Capire perché Klabhouse ha ratio 58,4 LOC/ora (vs media 43,0) e decidere se includere/escludere

---

## Dati Confronto

### LOC per Tipo Codice

| Progetto | Controller LOC | Modelli LOC | Controller/Modello Ratio |
|----------|----------------|-------------|--------------------------|
| **Mscarichi** | 4.668 | 1.679 | 2.78:1 |
| **Cactusdashboard** | 3.537 | 826 | 4.28:1 |
| **Klabhouse** | 7.187 | 2.590 | 2.77:1 |

**Osservazioni:**
- Klabhouse ha più LOC in controller e modelli, ma ratio simile a mscarichi
- Cactusdashboard ha ratio più alto (più controller per modello)

### Seeders Analisi

| Progetto | Seeders Totali | Seeders con Factory/Faker | LOC Seeders Factory |
|----------|----------------|---------------------------|---------------------|
| **Mscarichi** | 2 | - | - |
| **Cactusdashboard** | 8 | - | - |
| **Klabhouse** | 42 | 1 | 330 LOC |

**Osservazioni:**
- Klabhouse ha 42 seeders (vs 2-8 altri progetti)
- Solo 1 seeder usa factory/Faker (330 LOC)
- **4.558 LOC di seeders sono dati reali**, non solo test

### Ratio LOC/Ora Dettagliato

| Progetto | LOC Totali | Ore | LOC/Ora | Componenti |
|----------|------------|-----|---------|------------|
| **Mscarichi** | 11.247 | 306 | 36,7 | 39 controller, 22 modelli, 148 viste |
| **Cactusdashboard** | 9.058 | 268 | 33,8 | 27 controller, 13 modelli, 111 viste |
| **Klabhouse** | 32.407 | 555 | 58,4 | 43 controller, 32 modelli, 234 viste |

**Analisi:**
- Klabhouse ha **3x più LOC** di cactusdashboard ma solo **2x più ore**
- Klabhouse ha più componenti (controller, modelli, viste) ma ratio LOC/componente è simile

---

## Fattori che Spiegano l'Outlier

### 1. Seeders Dati Reali
- **4.558 LOC di seeders reali** (non factory)
- Probabilmente dati di configurazione, lookup tables, dati iniziali
- Questi seeders richiedono meno tempo di sviluppo (più copia/incolla/modifica)

### 2. Componenti Riutilizzabili
- Klabhouse potrebbe avere più componenti riutilizzabili
- Codice più modulare = meno tempo per funzionalità simili

### 3. Pattern di Sviluppo
- Sviluppo più recente (2025) = più esperienza
- Uso di pattern consolidati = sviluppo più veloce

### 4. Complessità Codice
- Ratio controller/modello simile a mscarichi (2.77:1)
- Non sembra codice più semplice, ma più organizzato

---

## Decisione: Includere o Escludere?

### Opzione A: Includere Klabhouse (Peso Normale)
**Pro:**
- Progetto reale e completo
- Pattern di sviluppo più recente (rappresenta produttività attuale)
- Dati reali, non teorici

**Contro:**
- Ratio molto alto (58,4 vs 33,8-36,7)
- Seeders potrebbero sovrastimare produttività
- Potrebbe non essere rappresentativo

**Media LOC/Ora:** (36,7 + 33,8 + 58,4) ÷ 3 = **43,0 LOC/ora**

### Opzione B: Escludere Klabhouse
**Pro:**
- Media più conservativa
- Evita distorsione da outlier

**Contro:**
- Perde dati progetto più recente
- Media basata su solo 2 progetti (campione troppo piccolo)
- Ignora miglioramento produttività nel tempo

**Media LOC/Ora:** (36,7 + 33,8) ÷ 2 = **35,3 LOC/ora**

### Opzione C: Includere con Peso Ridotto
**Pro:**
- Considera outlier ma non lo sovrappesa
- Media più rappresentativa

**Contro:**
- Peso arbitrario (es. 50% peso normale)

**Media LOC/Ora Ponderata (50% peso Klabhouse):**
- (36,7 × 1 + 33,8 × 1 + 58,4 × 0.5) ÷ 2.5 = **40,1 LOC/ora**

### Opzione D: Escludere Seeders da Klabhouse
**Pro:**
- Focus su codice applicativo (controller, modelli, viste)
- Più comparabile con altri progetti

**Contro:**
- Seeders sono parte del lavoro (anche se più veloci)

**LOC senza Seeders:** 32.407 - 4.888 = **27.519 LOC**  
**Ratio LOC/Ora:** 27.519 ÷ 555 = **49,6 LOC/ora** (ancora alto)

---

## Raccomandazione

### Scelta Consigliata: **Opzione A (Includere con Peso Normale)**

**Giustificazione:**
1. **Klabhouse è progetto reale** e rappresenta produttività più recente
2. **Seeders sono parte del lavoro** (anche se più veloci, sono codice scritto)
3. **Media 43,0 LOC/ora è conservativa** per stime future (considerando miglioramento esperienza)
4. **Campione di 3 progetti è già piccolo**, escluderne uno lo ridurrebbe troppo

### Media LOC/Ora Finale: **43,0 LOC/ora**

**Range:**
- **Minimo (escludendo Klabhouse):** 35,3 LOC/ora
- **Massimo (solo Klabhouse):** 58,4 LOC/ora
- **Consigliato (media 3 progetti):** 43,0 LOC/ora

### Note per Uso

1. **Per stime conservative:** Usare 35-40 LOC/ora
2. **Per stime realistiche:** Usare 40-45 LOC/ora
3. **Per stime ottimistiche:** Usare 45-50 LOC/ora

---

**Prossimi Step:**
- Validare stime LOC L'Altramusica con ratio corretto (43,0 LOC/ora)
- Eseguire analisi sensibilità

