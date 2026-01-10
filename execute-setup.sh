#!/bin/bash

# Script per eseguire i comandi di setup
# Esegui questo script nel terminale: ./execute-setup.sh

echo "🔧 Setup Gestionale L'Altramusica - Valet Configuration"
echo ""

# 1. Link Valet
echo "📌 Step 1: Link Valet..."
valet link gestionale.altramusica
if [ $? -eq 0 ]; then
    echo "✅ Link creato con successo"
else
    echo "❌ Errore nel link - potrebbe richiedere sudo"
fi
echo ""

# 2. Secure (HTTPS)
echo "🔒 Step 2: Abilitazione HTTPS..."
valet secure gestionale.altramusica
if [ $? -eq 0 ]; then
    echo "✅ HTTPS abilitato"
else
    echo "❌ Errore nell'abilitazione HTTPS - potrebbe richiedere sudo"
fi
echo ""

# 3. Database - Prova diverse opzioni
echo "🗄️  Step 3: Creazione Database..."

# Prova con password
mysql -u root -p'Freelancer2024!' -e "CREATE DATABASE IF NOT EXISTS gestionale_altramusica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
if [ $? -eq 0 ]; then
    echo "✅ Database creato con password"
else
    # Prova senza password
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS gestionale_altramusica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "✅ Database creato senza password"
    else
        echo "❌ Errore nella creazione del database"
        echo "   Prova manualmente: mysql -u root -p -e 'CREATE DATABASE gestionale_altramusica;'"
    fi
fi
echo ""

# 4. Migrations
echo "📦 Step 4: Esecuzione Migrations..."
cd /Users/mistre/develop/gestionale-laltramusica
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo "✅ Migrations completate"
else
    echo "❌ Errore nelle migrations - verifica la connessione al database"
fi
echo ""

# 5. Verifica
echo "🔍 Step 5: Verifica configurazione..."
echo "Link Valet:"
valet links | grep gestionale || echo "  Nessun link trovato"
echo ""
echo "Database:"
mysql -u root -p'Freelancer2024!' -e "SHOW DATABASES LIKE 'gestionale_altramusica';" 2>/dev/null || \
mysql -u root -e "SHOW DATABASES LIKE 'gestionale_altramusica';" 2>/dev/null || \
echo "  Impossibile verificare - controlla manualmente"
echo ""

echo "✅ Setup completato!"
echo ""
echo "🌐 Il sito dovrebbe essere disponibile su:"
echo "   - HTTP:  http://gestionale.altramusica.test"
echo "   - HTTPS: https://gestionale.altramusica.test"
echo ""
echo "📝 Se alcuni step sono falliti, eseguili manualmente."

