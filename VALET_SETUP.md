# Setup Valet per Gestionale L'Altramusica

## ✅ Configurazione Completata

Il file `.env` è stato configurato con:
- **Database**: `gestionale_altramusica`
- **Username**: `root`
- **Password**: `Freelancer2024!`
- **APP_URL**: `https://gestionale.altramusica.test`

## 📋 Comandi da Eseguire

### 1. Configura Valet (richiede sudo)

Esegui nel terminale:

```bash
cd /Users/mistre/develop/gestionale-laltramusica

# Linka il progetto
valet link gestionale.altramusica

# Abilita HTTPS
valet secure gestionale.altramusica
```

Oppure esegui lo script automatico:
```bash
./setup-valet.sh
```

### 2. Crea il Database MySQL

Se la password `Freelancer2024!` non funziona, prova una di queste opzioni:

**Opzione A - Con password:**
```bash
mysql -u root -p'Freelancer2024!' -e "CREATE DATABASE IF NOT EXISTS gestionale_altramusica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Opzione B - Senza password (se MySQL non ha password):**
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS gestionale_altramusica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**Opzione C - Con prompt password:**
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS gestionale_altramusica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Esegui le Migrations

```bash
php artisan migrate
```

### 4. Verifica

Apri nel browser:
- **HTTP**: http://gestionale.altramusica.test
- **HTTPS**: https://gestionale.altramusica.test

## 🔧 Troubleshooting

### Se Valet non funziona:
```bash
valet restart
valet links  # Verifica i link attivi
```

### Se il database non si connette:
1. Verifica che MySQL sia in esecuzione: `brew services list | grep mysql`
2. Verifica le credenziali nel file `.env`
3. Prova a connetterti manualmente: `mysql -u root -p`

### Se le migrations falliscono:
```bash
php artisan migrate:fresh  # ATTENZIONE: cancella tutti i dati!
```

## 📝 Note

- Il dominio `.test` è gestito automaticamente da Valet
- Se usi HTTPS, potrebbe essere necessario accettare il certificato autofirmato nel browser
- Le migrations sono già state eseguite su SQLite, quindi potrebbe essere necessario rifarle su MySQL
