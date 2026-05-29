<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R9 · Controllo finale — Validazione comunicazioni mirate (attività #8548).
 *
 * CONTESTO: R9 (#8529, doc 34) è SOLO DESIGN. Il §10 "Impatti tecnici" dichiara
 * esplicitamente che l'implementazione NON è parte di R9. La tabella `communications`
 * ESISTE (migration 2025_12_11 + 2025_12_22) ma è DORMIENTE: nessun model
 * Communication, nessun CommunicationService, nessuna classe Mailable (cartella
 * app/Mail assente), nessun controller, nessuna rotta (in routes/web.php la risorsa
 * è commentata "communications evolutivo"), nessuna vista. Manca anche la tabella
 * `communication_recipients` (esito per-destinatario, §10) e `MAIL_MAILER=log`
 * in .env (non SMTP reale).
 *
 * Di conseguenza un E2E su controller/UI NON è eseguibile (= "blocco" previsto dalla
 * richiesta). Questo test fa due cose oneste:
 *   1) VALIDA I FLUSSI DEL DESIGN a livello dati+framework: compone un messaggio,
 *      SEGMENTA i destinatari (corso/anno/stato), risolve i contatti famiglia con
 *      regola consenso GDPR e dedup fratelli, INVIA email reale (transport di test =
 *      analogo SMTP) e logga, prepara BOZZA SMS/WhatsApp (numeri E.164 + link wa.me,
 *      nessun invio) e logga. Tutto su entità esistenti + tabella `communications`.
 *   2) FOTOGRAFA IL BLOCCO: assert che model/service/Mailable/recipients/rotte sono
 *      assenti e che lo schema dormiente non copre ancora il design §10, così il test
 *      è un registro vivo del gap da colmare in fase di implementazione.
 *
 * Trascrizioni: parte 1 r.74-76, 178-180 — parte 2 r.10.
 */
class ComunicazioniMirateE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = AcademicYear::create([
            'name' => '2025/26',
            'slug' => '2025-26',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper di scena (usano SOLO model esistenti; communications via DB)
    // ─────────────────────────────────────────────────────────────────────────

    private function makeStudent(string $first, string $last): Student
    {
        return Student::create([
            'first_name' => $first, 'last_name' => $last, 'birth_date' => '2012-01-01',
        ]);
    }

    /** Crea un guardian e lo lega allo studente (pivot student_guardian). */
    private function makeGuardian(Student $student, array $attrs, bool $primary = true): Guardian
    {
        $g = Guardian::create(array_merge([
            'first_name' => 'G', 'last_name' => 'Tutore', 'relationship' => 'mother',
            'privacy_consent' => true,
        ], $attrs));

        $student->guardians()->attach($g->id, ['is_primary' => $primary]);

        return $g;
    }

    private function offering(string $courseCode = 'PF1'): CourseOffering
    {
        $type = CourseType::firstOrCreate(['code' => 'PF'], ['name' => 'Pianoforte', 'duration_minutes' => 30]);
        $course = Course::firstOrCreate(['code' => $courseCode], ['course_type_id' => $type->id, 'name' => 'Pianoforte']);

        return CourseOffering::firstOrCreate(
            ['course_id' => $course->id, 'academic_year_id' => $this->year->id],
            ['status' => 'active'],
        );
    }

    private function enroll(Student $student, CourseOffering $offering, string $status = 'active'): Enrollment
    {
        return Enrollment::create([
            'academic_year_id' => $this->year->id,
            'student_id' => $student->id,
            'course_offering_id' => $offering->id,
            'enrollment_date' => '2025-09-10',
            'start_date' => '2025-09-15',
            'status' => $status,
        ]);
    }

    private function studentYear(Student $student, string $status, bool $consent = true): StudentYear
    {
        return StudentYear::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->year->id,
            'status' => $status,
            'privacy_consent' => $consent,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MOTORE RISOLUZIONE DESTINATARI (doc §5) esercitato a livello dati.
    // Da segmento → studenti → genitori → contatti; applica consenso (salvo
    // operativa), regola "tutti i genitori / solo primario", dedup, validazione.
    // Ritorna inclusi + esclusi-con-motivo (alimenta anteprima §3 e drawer §5).
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * @param array $segment ['course_offering_id'?, 'enrollment_status'?, 'year_status'?]
     * @param string $channel 'email' | 'sms' | 'whatsapp'
     */
    private function resolveRecipients(array $segment, string $channel = 'email', bool $allParents = true, bool $operational = false): array
    {
        // 1) segmento → studenti (AND dei filtri opzionali; vuoto = tutti)
        $studentIds = collect();

        if (isset($segment['enrollment_status']) || isset($segment['course_offering_id'])) {
            $q = Enrollment::query()->where('academic_year_id', $this->year->id);
            if (isset($segment['course_offering_id'])) {
                $q->where('course_offering_id', $segment['course_offering_id']);
            }
            if (isset($segment['enrollment_status'])) {
                $q->where('status', $segment['enrollment_status']);
            }
            $studentIds = $q->pluck('student_id');
        }

        if (isset($segment['year_status'])) {
            $ys = StudentYear::query()
                ->where('academic_year_id', $this->year->id)
                ->where('status', $segment['year_status'])
                ->pluck('student_id');
            $studentIds = $studentIds->isEmpty() ? $ys : $studentIds->intersect($ys);
        }

        $students = Student::with('guardians')->whereIn('id', $studentIds->unique())->get();

        // 2) studenti → genitori → contatti; consenso, dedup, validazione
        $included = [];
        $excluded = [];
        $seenContacts = [];

        foreach ($students as $student) {
            /** @var Collection $guardians */
            $guardians = $allParents
                ? $student->guardians
                : $student->guardians->filter(fn ($g) => $g->pivot->is_primary)->take(1);

            if ($guardians->isEmpty()) {
                $excluded[] = ['student' => $student->id, 'reason' => 'nessun genitore collegato'];
                continue;
            }

            $reachedForStudent = false;

            foreach ($guardians as $g) {
                // consenso: escluso se manca, salvo comunicazione operativa
                if (! $operational && ! $g->privacy_consent) {
                    $excluded[] = ['student' => $student->id, 'guardian' => $g->id, 'reason' => 'nessun consenso'];
                    continue;
                }

                $contacts = $channel === 'email'
                    ? $this->emailContacts($g, $allParents)
                    : $this->phoneContacts($g);

                if (empty($contacts)) {
                    $excluded[] = ['student' => $student->id, 'guardian' => $g->id, 'reason' => $channel === 'email' ? 'nessuna email' : 'nessun cellulare valido'];
                    continue;
                }

                foreach ($contacts as $contact) {
                    if (isset($seenContacts[$contact])) {
                        continue; // dedup fratelli / stesso recapito
                    }
                    $seenContacts[$contact] = true;
                    $included[] = ['student' => $student->id, 'guardian' => $g->id, 'contact' => $contact];
                    $reachedForStudent = true;
                }
            }

            // se nessun contatto utile è stato raccolto e non è già stato escluso col motivo
            if (! $reachedForStudent && $guardians->every(fn ($g) => $operational || $g->privacy_consent)) {
                // i motivi puntuali sono già in $excluded (no email / no cell)
            }
        }

        return ['included' => $included, 'excluded' => $excluded];
    }

    private function emailContacts(Guardian $g, bool $allParents): array
    {
        $emails = $allParents
            ? array_filter([$g->email_1, $g->email_2, $g->email_3])
            : array_filter([$g->email_1]);

        return array_values(array_filter($emails, fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    }

    private function phoneContacts(Guardian $g): array
    {
        $raw = array_filter([$g->cell_1, $g->cell_2, $g->cell_3, $g->cell_4]);
        $e164 = array_map(fn ($n) => $this->toE164($n), $raw);

        return array_values(array_filter($e164));
    }

    /** Normalizza un cellulare IT in E.164 (+39…); null se non valido (doc §4/§10). */
    private function toE164(?string $number): ?string
    {
        if (! $number) {
            return null;
        }
        $clean = preg_replace('/[\s\-\.\(\)]/', '', $number);
        if (str_starts_with($clean, '+')) {
            return preg_match('/^\+\d{8,15}$/', $clean) ? $clean : null;
        }
        if (str_starts_with($clean, '00')) {
            $clean = '+' . substr($clean, 2);

            return preg_match('/^\+\d{8,15}$/', $clean) ? $clean : null;
        }
        // numero nazionale IT (cellulare 9-10 cifre, prefisso 3)
        if (preg_match('/^3\d{8,9}$/', $clean)) {
            return '+39' . $clean;
        }

        return null; // malformato → escluso
    }

    /** Registra una comunicazione nel log (tabella dormiente `communications`). */
    private function logCommunication(string $type, string $subject, string $message, array $recipients, string $status, ?User $by = null): int
    {
        return DB::table('communications')->insertGetId([
            'type' => $type,
            'subject' => $subject,
            'message' => $message,
            'recipients' => json_encode($recipients),
            'status' => $status,
            'sent_by_user_id' => $by?->id,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. SUBSTRATO — la tabella dormiente esiste e regge il log (deve passare)
    // ─────────────────────────────────────────────────────────────────────────

    /** doc §1/§10: la migration c'è — `communications` con le colonne base del log. */
    public function test_substrato_tabella_communications_pronta(): void
    {
        $this->assertTrue(Schema::hasTable('communications'));
        $this->assertTrue(Schema::hasColumns('communications', [
            'type', 'subject', 'message', 'recipients', 'status',
            'sent_by_user_id', 'sent_at', 'template_name', 'error_message',
        ]));
    }

    /** doc §4: l'enum `type` accetta i tre canali del design (email/sms/whatsapp). */
    public function test_substrato_canali_email_sms_whatsapp_accettati(): void
    {
        foreach (['email', 'sms', 'whatsapp'] as $canale) {
            $id = $this->logCommunication($canale, 'Test', 'Corpo', [], 'sent');
            $this->assertSame($canale, DB::table('communications')->where('id', $id)->value('type'));
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. SEGMENTAZIONE (doc §2/§3) — corso / anno / stato
    // ─────────────────────────────────────────────────────────────────────────

    /** doc §2: segmento per corso + anno + stato ISCRIZIONE = solo iscritti attivi a quel corso. */
    public function test_segmento_corso_anno_stato_iscrizione_attivi(): void
    {
        $pf = $this->offering('PF1');
        $chitarra = $this->offering('CH1');

        $mario = $this->makeStudent('Mario', 'Rossi');
        $this->makeGuardian($mario, ['email_1' => 'anna.rossi@mail.it']);
        $this->enroll($mario, $pf, 'active');

        $luca = $this->makeStudent('Luca', 'Bianchi');
        $this->makeGuardian($luca, ['email_1' => 'p.bianchi@mail.it']);
        $this->enroll($luca, $pf, 'suspended'); // sospeso → fuori dal segmento "attivi"

        $gaia = $this->makeStudent('Gaia', 'Neri');
        $this->makeGuardian($gaia, ['email_1' => 'neri@mail.it']);
        $this->enroll($gaia, $chitarra, 'active'); // altro corso → fuori

        $res = $this->resolveRecipients(
            ['course_offering_id' => $pf->id, 'enrollment_status' => 'active'],
            'email',
        );

        $this->assertCount(1, $res['included']); // solo Mario
        $this->assertSame('anna.rossi@mail.it', $res['included'][0]['contact']);
    }

    /** doc §2: "Stato anagrafica" (StudentYear) ≠ stato iscrizione — segmento campagna. */
    public function test_segmento_stato_anagrafica_interessati(): void
    {
        $a = $this->makeStudent('Aldo', 'A');
        $this->makeGuardian($a, ['email_1' => 'aldo@mail.it']);
        $this->studentYear($a, 'interested');

        $b = $this->makeStudent('Bea', 'B');
        $this->makeGuardian($b, ['email_1' => 'bea@mail.it']);
        $this->studentYear($b, 'enrolled'); // già iscritto → fuori da "interessati"

        $res = $this->resolveRecipients(['year_status' => 'interested'], 'email');

        $this->assertCount(1, $res['included']);
        $this->assertSame('aldo@mail.it', $res['included'][0]['contact']);
    }

    /** doc §2: stato iscrizione "Sospeso" — segmento solleciti/recupero. */
    public function test_segmento_iscritti_sospesi(): void
    {
        $pf = $this->offering('PF1');

        $s1 = $this->makeStudent('S1', 'X');
        $this->makeGuardian($s1, ['email_1' => 's1@mail.it']);
        $this->enroll($s1, $pf, 'suspended');

        $s2 = $this->makeStudent('S2', 'Y');
        $this->makeGuardian($s2, ['email_1' => 's2@mail.it']);
        $this->enroll($s2, $pf, 'active');

        $res = $this->resolveRecipients(['enrollment_status' => 'suspended'], 'email');

        $this->assertCount(1, $res['included']);
        $this->assertSame('s1@mail.it', $res['included'][0]['contact']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. RISOLUZIONE CONTATTI + CONSENSO GDPR (doc §5) — la parte delicata
    // ─────────────────────────────────────────────────────────────────────────

    /** doc §0.4/§5 + Privacy #1: guardian senza `privacy_consent` → ESCLUSO con motivo. */
    public function test_consenso_esclude_guardian_senza_privacy_consent(): void
    {
        $pf = $this->offering('PF1');

        $ok = $this->makeStudent('Ok', 'Consenso');
        $this->makeGuardian($ok, ['email_1' => 'ok@mail.it', 'privacy_consent' => true]);
        $this->enroll($ok, $pf, 'active');

        $no = $this->makeStudent('No', 'Consenso');
        $this->makeGuardian($no, ['email_1' => 'no@mail.it', 'privacy_consent' => false]);
        $this->enroll($no, $pf, 'active');

        $res = $this->resolveRecipients(['enrollment_status' => 'active'], 'email');

        $this->assertCount(1, $res['included']);
        $this->assertSame('ok@mail.it', $res['included'][0]['contact']);
        $this->assertNotEmpty($res['excluded']);
        $this->assertSame('nessun consenso', $res['excluded'][0]['reason']);
    }

    /** doc §5: flag "Comunicazione di servizio (operativa)" → bypassa il filtro consenso. */
    public function test_comunicazione_operativa_bypassa_consenso(): void
    {
        $pf = $this->offering('PF1');

        $no = $this->makeStudent('No', 'Consenso');
        $this->makeGuardian($no, ['email_1' => 'no@mail.it', 'privacy_consent' => false]);
        $this->enroll($no, $pf, 'active');

        $promozionale = $this->resolveRecipients(['enrollment_status' => 'active'], 'email', operational: false);
        $this->assertCount(0, $promozionale['included']); // escluso

        $operativa = $this->resolveRecipients(['enrollment_status' => 'active'], 'email', operational: true);
        $this->assertCount(1, $operativa['included']); // avviso necessario → raggiunto
        $this->assertSame('no@mail.it', $operativa['included'][0]['contact']);
    }

    /** doc §5: stesso indirizzo per più studenti (fratelli) → UNA sola email (dedup). */
    public function test_dedup_fratelli_un_solo_indirizzo(): void
    {
        $pf = $this->offering('PF1');

        $mario = $this->makeStudent('Mario', 'Rossi');
        $gMario = $this->makeGuardian($mario, ['email_1' => 'famiglia.rossi@mail.it']);
        $this->enroll($mario, $pf, 'active');

        // fratello: STESSO guardian (stesso indirizzo) collegato anche a Sara
        $sara = $this->makeStudent('Sara', 'Rossi');
        $sara->guardians()->attach($gMario->id, ['is_primary' => true]);
        $this->enroll($sara, $pf, 'active');

        $res = $this->resolveRecipients(['enrollment_status' => 'active'], 'email');

        $this->assertCount(1, $res['included']); // un solo invio all'indirizzo condiviso
        $this->assertSame('famiglia.rossi@mail.it', $res['included'][0]['contact']);
    }

    /** doc §5: regola "Tutti i genitori" vs "Solo primario". */
    public function test_tutti_genitori_vs_solo_primario(): void
    {
        $pf = $this->offering('PF1');
        $mario = $this->makeStudent('Mario', 'Rossi');
        $this->makeGuardian($mario, ['email_1' => 'mamma@mail.it'], primary: true);
        $this->makeGuardian($mario, ['first_name' => 'Papa', 'email_1' => 'papa@mail.it'], primary: false);
        $this->enroll($mario, $pf, 'active');

        $tutti = $this->resolveRecipients(['enrollment_status' => 'active'], 'email', allParents: true);
        $this->assertCount(2, $tutti['included']); // mamma + papà

        $soloPrimario = $this->resolveRecipients(['enrollment_status' => 'active'], 'email', allParents: false);
        $this->assertCount(1, $soloPrimario['included']);
        $this->assertSame('mamma@mail.it', $soloPrimario['included'][0]['contact']);
    }

    /** doc §3/§5: guardian senza recapito valido → ESCLUSO con motivo "nessuna email". */
    public function test_senza_recapito_finisce_negli_esclusi(): void
    {
        $pf = $this->offering('PF1');
        $tom = $this->makeStudent('Tom', 'Blu');
        $this->makeGuardian($tom, ['first_name' => 'Lia', 'email_1' => null]); // nessuna email
        $this->enroll($tom, $pf, 'active');

        $res = $this->resolveRecipients(['enrollment_status' => 'active'], 'email');

        $this->assertCount(0, $res['included']);
        $this->assertSame('nessuna email', $res['excluded'][0]['reason']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. EMAIL REALE (doc §4/§6) — invio via transport (analogo SMTP in test) + log
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * doc §4/§6: l'email parte DAVVERO. In ambiente di test il mailer usa il transport
     * `array` (MAIL_MAILER=array da phpunit.xml): l'invio attraversa l'intero stack del
     * framework e produce messaggi reali — analogo verificabile dell'invio SMTP. 1 mail
     * per destinatario incluso, con merge field risolti.
     */
    public function test_invio_email_reale_a_segmento_e_log(): void
    {
        $admin = User::factory()->create();
        $pf = $this->offering('PF1');

        $mario = $this->makeStudent('Mario', 'Rossi');
        $this->makeGuardian($mario, ['first_name' => 'Anna', 'email_1' => 'anna.rossi@mail.it']);
        $this->enroll($mario, $pf, 'active');

        $luca = $this->makeStudent('Luca', 'Bianchi');
        $this->makeGuardian($luca, ['first_name' => 'Paolo', 'email_1' => 'p.bianchi@mail.it']);
        $this->enroll($luca, $pf, 'active');

        $res = $this->resolveRecipients(['course_offering_id' => $pf->id, 'enrollment_status' => 'active'], 'email');
        $this->assertCount(2, $res['included']);

        $oggetto = 'Saggio di fine anno — info';
        $sentTo = [];
        foreach ($res['included'] as $rcpt) {
            $student = Student::find($rcpt['student']);
            // merge field {{studente}} risolto per-destinatario (doc §3)
            $corpo = "Gentile famiglia di {$student->first_name}, vi invitiamo al saggio.";
            Mail::raw($corpo, function ($m) use ($rcpt, $oggetto) {
                $m->to($rcpt['contact'])->subject($oggetto);
            });
            $sentTo[] = $rcpt['contact'];
        }

        // l'invio ha prodotto messaggi reali nel transport (analogo SMTP)
        $messages = Mail::getSymfonyTransport()->messages();
        $this->assertCount(2, $messages);

        // log per tracciabilità (§7): chi/quando/canale/destinatari/esito
        $logId = $this->logCommunication('email', $oggetto, 'Saggio…', $sentTo, 'sent', $admin);
        $row = DB::table('communications')->find($logId);
        $this->assertSame('email', $row->type);
        $this->assertSame($admin->id, $row->sent_by_user_id);
        $this->assertNotNull($row->sent_at);
        $this->assertEqualsCanonicalizing(['anna.rossi@mail.it', 'p.bianchi@mail.it'], json_decode($row->recipients, true));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. SMS / WHATSAPP — BOZZA da inviare a mano (doc §4) — NESSUN invio + log
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * doc §4/§10: per WhatsApp il sistema NON invia. Genera testo finale + lista numeri
     * E.164 + link wa.me per-contatto; logga come bozza ("da inviare a mano"). Verifichiamo
     * che NESSUNA email/SMS reale parta dal sistema su questo canale.
     */
    public function test_bozza_whatsapp_numeri_e164_link_e_log_senza_invio(): void
    {
        $admin = User::factory()->create();
        $pf = $this->offering('PF1');

        $mario = $this->makeStudent('Mario', 'Rossi');
        $this->makeGuardian($mario, ['cell_1' => '333 1234567']);
        $this->enroll($mario, $pf, 'active');

        $luca = $this->makeStudent('Luca', 'Bianchi');
        $this->makeGuardian($luca, ['cell_1' => '+39 340 7654321']);
        $this->enroll($luca, $pf, 'active');

        $res = $this->resolveRecipients(['enrollment_status' => 'active'], 'whatsapp');
        $numeri = array_column($res['included'], 'contact');

        // numeri normalizzati E.164 pronti per incolla / wa.me
        $this->assertEqualsCanonicalizing(['+393331234567', '+393407654321'], $numeri);

        $testo = 'Chiusura per ponte il 2 giugno. Lezioni sospese.';
        $link = 'https://wa.me/' . ltrim($numeri[0], '+') . '?text=' . rawurlencode($testo);
        $this->assertSame('https://wa.me/393331234567?text=Chiusura%20per%20ponte%20il%202%20giugno.%20Lezioni%20sospese.', $link);

        // bozza loggata, MA nessun invio reale (transport vuoto su questo canale)
        $this->logCommunication('whatsapp', 'Chiusura ponte', $testo, $numeri, 'sent', $admin);
        $this->assertCount(0, Mail::getSymfonyTransport()->messages());
        $this->assertSame('whatsapp', DB::table('communications')->where('type', 'whatsapp')->value('type'));
    }

    /** doc §4: cellulare malformato → escluso dai numeri (non finisce nella lista wa.me). */
    public function test_numero_malformato_escluso_dalla_bozza_sms(): void
    {
        $pf = $this->offering('PF1');
        $s = $this->makeStudent('S', 'X');
        $this->makeGuardian($s, ['cell_1' => '12']); // troppo corto / non IT → escluso
        $this->enroll($s, $pf, 'active');

        $res = $this->resolveRecipients(['enrollment_status' => 'active'], 'sms');

        $this->assertCount(0, $res['included']);
        $this->assertSame('nessun cellulare valido', $res['excluded'][0]['reason']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. BLOCCO — fotografia del gap di implementazione (doc §10, NON parte di R9)
    // ─────────────────────────────────────────────────────────────────────────

    /** §1/§10: il model Communication che mapperebbe la tabella dormiente NON esiste. */
    public function test_blocco_model_communication_assente(): void
    {
        $this->assertFalse(class_exists(\App\Models\Communication::class), 'Model Communication ancora dormiente');
    }

    /** §10: il service di risoluzione segmento → destinatari NON esiste ancora. */
    public function test_blocco_service_risoluzione_destinatari_assente(): void
    {
        $this->assertFalse(class_exists(\App\Services\CommunicationService::class), 'CommunicationService da implementare');
    }

    /** §4/§10: nessuna classe Mailable per l'invio email reale (cartella app/Mail assente). */
    public function test_blocco_mailable_communicationmail_assente(): void
    {
        $this->assertFalse(class_exists(\App\Mail\CommunicationMail::class), 'Mailable CommunicationMail da creare');
        $this->assertDirectoryDoesNotExist(app_path('Mail'));
    }

    /** §10: la tabella `communication_recipients` (esito per-destinatario) NON esiste. */
    public function test_blocco_tabella_communication_recipients_assente(): void
    {
        $this->assertFalse(Schema::hasTable('communication_recipients'), 'Esito per-destinatario (§10) da aggiungere');
    }

    /** §10: lo schema dormiente `communications` non copre ancora il design (snapshot segmento, flag operativa, contatori, stati manuali). */
    public function test_blocco_schema_dormiente_non_copre_design(): void
    {
        // il design (§10) prevede: channel/is_operational/segment(JSON)/contatori/stati draft|queued|manual_*
        $this->assertFalse(Schema::hasColumn('communications', 'is_operational'), 'flag operativa/GDPR da aggiungere');
        $this->assertFalse(Schema::hasColumn('communications', 'segment'), 'snapshot segmento da aggiungere');
        // oggi esiste solo un enum status sent|delivered|failed: niente draft/queued/manual_pending/manual_done
        $this->assertTrue(Schema::hasColumn('communications', 'status'));
    }

    /** §10: nessuna rotta admin per comunicazioni (resource commentata "evolutivo" in web.php). */
    public function test_blocco_rotte_communications_assenti(): void
    {
        $this->assertFalse(Route::has('admin.communications.index'));
        $this->assertFalse(Route::has('admin.communications.create'));
        $this->assertFalse(Route::has('admin.communications.store'));
        $this->assertFalse(Route::has('admin.communications.mark-sent'));
    }
}
