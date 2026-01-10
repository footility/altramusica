# Formula Corretta: Campo → Tempo (Dati Validati)

**Data:** Dicembre 2024  
**Correzione:** Rimossi dati errati, usati solo dati confermati

---

## Dati Confermati

### MsCarichi (UNICO DATO CONFERMATO)

| Metrica | Valore |
|---------|--------|
| **DEV UNITS Totali** | **1968** ✅ (confermato commit f7ba8aa) |
| **Campi Custom** | 86 |
| **Ore Sviluppo** | 290 ore |
| **DEV UNITS per Campo** | 1968 / 86 = **22,9 DEV UNITS/campo** |
| **Tempo per Campo** | 290 / 86 = **3,37 ore/campo** |

### Cactusboard (DATI NON CONFERMATI)

| Metrica | Valore |
|---------|--------|
| **DEV UNITS Totali** | ❓ **SCONOSCIUTO** |
| **Campi Custom** | 92 |
| **Ore Sviluppo** | 290 ore |
| **DEV UNITS per Campo** | ❓ Non calcolabile |
| **Tempo per Campo** | 290 / 92 = **3,15 ore/campo** |

---

## Formula Basata su Dato Unico Confermato

### Usando Solo MsCarichi (Dato Validato)

```
1 Campo Database = 22,9 DEV UNITS
1 Campo Database = 3,37 ore sviluppo
```

**Validità:** Basato su 1 progetto reale (MsCarichi)

**Precisione:** Media su 86 campi, deviazione non calcolabile (campione singolo)

---

## Alternativa: Usare LOC invece di DEV UNITS

### Dati LOC (Confermati per Entrambi)

| Progetto | LOC | Campi | LOC/Campo | Ore/Campo |
|----------|-----|-------|-----------|-----------|
| **MsCarichi** | 9.653 | 86 | 112,2 | 3,37 |
| **Cactusboard** | 7.368 | 92 | 80,0 | 3,15 |
| **MEDIA** | **8.510** | **89** | **96,1** | **3,26** |

**Formula LOC:**
```
1 Campo Database = 96 LOC
1 Campo Database = 3,26 ore sviluppo (media)
```

**Vantaggi:**
- ✅ Dati confermati per entrambi i progetti
- ✅ Media statistica su 2 progetti
- ✅ Range: 80-112 LOC/campo
- ✅ Tempo: 3,15-3,37 ore/campo

---

## Raccomandazione

**Usare formula LOC invece di DEV UNITS** perché:
1. ✅ Dati LOC sono confermati per entrambi i progetti
2. ✅ Media statistica più affidabile (2 progetti vs 1)
3. ✅ LOC è misurabile direttamente
4. ✅ Formula più semplice e verificabile

**Formula Finale Raccomandata:**
```
1 Campo Database = 96 LOC (media)
1 Campo Database = 3,26 ore sviluppo (media)
Range: 3,15 - 3,37 ore/campo
```

---

## Note

- **DEV UNITS:** Solo MsCarichi confermato (1968), Cactusboard sconosciuto
- **LOC:** Entrambi confermati, media più affidabile
- **Tempo:** Entrambi confermati, media 3,26 ore/campo
- **Precisione:** Media su 178 campi totali (86 + 92)

