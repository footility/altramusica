# 🔐 Accesso al Sistema Gestionale L'Altramusica

## 🌐 URL di Accesso

**Sito Web**: https://gestionale.altramusica.test

## 👤 Credenziali di Accesso

### Utente Amministratore

- **Email**: `admin@altramusica.test`
- **Password**: `admin123`
- **Ruolo**: Admin (accesso completo a tutte le funzionalità)

## 📝 Come Accedere

1. Apri il browser e vai su: **https://gestionale.altramusica.test**
2. Verrai reindirizzato automaticamente alla pagina di login
3. Inserisci le credenziali:
   - Email: `admin@altramusica.test`
   - Password: `admin123`
4. Clicca su "Login"

## 🎯 Dopo il Login

Dopo aver effettuato l'accesso, verrai reindirizzato alla **Dashboard Admin** dove potrai:

- ✅ Gestire Studenti
- ✅ Gestire Genitori/Tutori
- ✅ Gestire Docenti
- ✅ Gestire Corsi
- ✅ Gestire Iscrizioni
- ✅ Gestire Fatture
- ✅ Gestire Strumenti
- ✅ Gestire Contratti
- ✅ Gestire Esami

## 🔒 Cambiare la Password

**IMPORTANTE**: Cambia la password di default dopo il primo accesso!

Per cambiare la password:
1. Accedi al sistema
2. Clicca sul tuo nome in alto a destra
3. Seleziona "Impostazioni" (quando implementato)
4. Oppure modifica direttamente nel database:
   ```bash
   php artisan tinker
   $user = \App\Models\User::where('email', 'admin@altramusica.test')->first();
   $user->password = bcrypt('TuaNuovaPassword');
   $user->save();
   ```

## 👥 Creare Altri Utenti

Puoi creare altri utenti tramite:
1. **Registrazione** (se abilitata): https://gestionale.altramusica.test/register
2. **Tinker** (da terminale):
   ```bash
   php artisan tinker
   \App\Models\User::create([
       'name' => 'Nome Utente',
       'email' => 'email@example.com',
       'password' => bcrypt('password'),
       'role' => 'admin', // oppure 'teacher', 'guardian', 'student'
   ]);
   ```

## 🛠️ Ruoli Disponibili

- **admin**: Accesso completo a tutte le funzionalità
- **teacher**: Accesso alle funzionalità per docenti
- **guardian**: Accesso limitato per genitori/tutori
- **student**: Accesso limitato per studenti

## ⚠️ Note di Sicurezza

- Cambia la password di default immediatamente
- Non condividere le credenziali
- Usa password forti per gli utenti di produzione


