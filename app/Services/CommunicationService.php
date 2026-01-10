<?php

namespace App\Services;

use App\Models\Communication;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CommunicationService
{
    /**
     * Invia comunicazione email
     */
    public function sendEmail($to, $subject, $message, $options = [])
    {
        try {
            Mail::raw($message, function ($mail) use ($to, $subject, $options) {
                $mail->to($to)
                     ->subject($subject);
                
                if (!empty($options['from'])) {
                    $mail->from($options['from'], $options['from_name'] ?? 'L\'Altramusica');
                }
            });
            
            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Errore invio email: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Invia comunicazione SMS (placeholder - richiede integrazione gateway)
     */
    public function sendSMS($to, $message, $options = [])
    {
        // TODO: Integrare gateway SMS (es. Twilio, Nexmo)
        Log::info("SMS da inviare a {$to}: {$message}");
        return ['success' => false, 'error' => 'Gateway SMS non configurato'];
    }
    
    /**
     * Invia comunicazione WhatsApp (placeholder - richiede integrazione API)
     */
    public function sendWhatsApp($to, $message, $options = [])
    {
        // TODO: Integrare API WhatsApp (es. Twilio WhatsApp API)
        Log::info("WhatsApp da inviare a {$to}: {$message}");
        return ['success' => false, 'error' => 'Gateway WhatsApp non configurato'];
    }
    
    /**
     * Crea e invia comunicazione
     */
    public function sendCommunication($data)
    {
        $communication = Communication::create([
            'student_id' => $data['student_id'] ?? null,
            'guardian_id' => $data['guardian_id'] ?? null,
            'type' => $data['type'],
            'template_name' => $data['template_name'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'recipients' => $data['recipients'] ?? null,
            'sent_by_user_id' => auth()->id(),
            'status' => 'sent',
        ]);
        
        $result = null;
        $recipient = $this->getRecipient($data);
        
        if (!$recipient) {
            $communication->update([
                'status' => 'failed',
                'error_message' => 'Destinatario non trovato',
            ]);
            return $communication;
        }
        
        switch ($data['type']) {
            case 'email':
                $result = $this->sendEmail(
                    $recipient['email'],
                    $data['subject'],
                    $data['message'],
                    $data['options'] ?? []
                );
                break;
            case 'sms':
                $result = $this->sendSMS(
                    $recipient['phone'],
                    $data['message'],
                    $data['options'] ?? []
                );
                break;
            case 'whatsapp':
                $result = $this->sendWhatsApp(
                    $recipient['phone'],
                    $data['message'],
                    $data['options'] ?? []
                );
                break;
        }
        
        if ($result && $result['success']) {
            $communication->update([
                'sent_at' => now(),
                'status' => 'delivered',
            ]);
        } else {
            $communication->update([
                'status' => 'failed',
                'error_message' => $result['error'] ?? 'Errore sconosciuto',
            ]);
        }
        
        return $communication;
    }
    
    /**
     * Invia comunicazione massiva
     */
    public function sendBulk($type, $subject, $message, $filters = [])
    {
        $recipients = $this->getRecipientsFromFilters($filters);
        $results = [];
        
        foreach ($recipients as $recipient) {
            $data = [
                'type' => $type,
                'subject' => $subject,
                'message' => $message,
                'student_id' => $recipient['student_id'] ?? null,
                'guardian_id' => $recipient['guardian_id'] ?? null,
                'recipients' => [$recipient],
            ];
            
            $results[] = $this->sendCommunication($data);
        }
        
        return $results;
    }
    
    /**
     * Ottiene destinatario da student_id o guardian_id
     */
    protected function getRecipient($data)
    {
        if (!empty($data['student_id'])) {
            $student = Student::find($data['student_id']);
            if ($student) {
                // Prendi email/telefono dal primo genitore o dallo studente
                $guardian = $student->guardians()->where('is_primary', true)->first();
                if ($guardian && $guardian->email) {
                    return ['email' => $guardian->email, 'phone' => $guardian->phone];
                }
            }
        }
        
        if (!empty($data['guardian_id'])) {
            $guardian = Guardian::find($data['guardian_id']);
            if ($guardian) {
                return ['email' => $guardian->email, 'phone' => $guardian->phone];
            }
        }
        
        return null;
    }
    
    /**
     * Ottiene destinatari da filtri
     */
    protected function getRecipientsFromFilters($filters)
    {
        $recipients = [];
        
        if (!empty($filters['student_ids'])) {
            $students = Student::whereIn('id', $filters['student_ids'])->get();
            foreach ($students as $student) {
                $guardians = $student->guardians;
                foreach ($guardians as $guardian) {
                    $recipients[] = [
                        'student_id' => $student->id,
                        'guardian_id' => $guardian->id,
                        'email' => $guardian->email,
                        'phone' => $guardian->phone,
                    ];
                }
            }
        }
        
        return $recipients;
    }
}

