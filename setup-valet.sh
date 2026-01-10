#!/bin/bash

# Script per configurare Valet per il progetto Gestionale L'Altramusica

echo "🔧 Configurazione Valet per gestionale.altramusica.test"

# Vai nella directory del progetto
cd /Users/mistre/develop/gestionale-laltramusica

# Linka il progetto a Valet
echo "📌 Link del progetto a Valet..."
valet link gestionale.altramusica

# Abilita HTTPS (opzionale)
echo "🔒 Abilitazione HTTPS..."
valet secure gestionale.altramusica

# Verifica il link
echo "✅ Verifica link creati:"
valet links | grep gestionale

echo ""
echo "✅ Setup completato!"
echo "🌐 Il sito è disponibile su:"
echo "   - HTTP:  http://gestionale.altramusica.test"
echo "   - HTTPS: https://gestionale.altramusica.test"
echo ""
echo "📝 Prossimi passi:"
echo "   1. Verifica che il database MySQL esista:"
echo "      mysql -u root -p'Freelancer2024!' -e 'SHOW DATABASES LIKE \"gestionale_altramusica\";'"
echo ""
echo "   2. Se il database non esiste, crealo:"
echo "      mysql -u root -p'Freelancer2024!' -e 'CREATE DATABASE gestionale_altramusica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'"
echo ""
echo "   3. Esegui le migrations:"
echo "      php artisan migrate"

