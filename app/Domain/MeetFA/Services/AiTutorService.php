<?php

namespace App\Domain\MeetFA\Services;

use App\Domain\MeetFA\Models\MeetRoom;
use App\Domain\MeetFA\Models\MeetChatMessage;

class AiTutorService
{
    public function processMessage(MeetRoom $room, string $message): ?string
    {
        // Simple keyword-based AI for MVP
        $msg = strtolower($message);

        if (str_contains($msg, 'ajuda') || str_contains($msg, '@tutor')) {
            if (str_contains($msg, 'prova') || str_contains($msg, 'avaliação')) {
                return "As avaliações desta disciplina estão disponíveis no portal do aluno. Consulte o calendário acadêmico.";
            }
            if (str_contains($msg, 'presença') || str_contains($msg, 'falta')) {
                return "Sua presença é registrada automaticamente ao permanecer na sala. O mínimo exigido é de {$room->min_presence_minutes} minutos.";
            }
            if (str_contains($msg, 'material') || str_contains($msg, 'slide')) {
                return "O material de aula será disponibilizado pelo professor no ambiente virtual de aprendizagem após a aula.";
            }
            
            return "Olá! Sou o Tutor Virtual da Faculdade Anasps. Posso ajudar com dúvidas sobre provas, presença e materiais. Como posso auxiliar?";
        }

        return null;
    }
}
