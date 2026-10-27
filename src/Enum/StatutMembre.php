<?php

namespace App\Enum;

enum StatutMembre: string
{
    case ACTIF = 'actif';
    case SUSPENDU = 'suspendu';
    case EN_ATTENTE = 'en_attente';
    case EXCLU = 'exclu';
}
