<?php

namespace App\Enum;

enum StatutCampagne: string
{
    case EN_COURS = 'en_cours';
    case SUSPENDUE = 'suspendue';
    case CLOTUREE = 'cloturee';
    case ANNULEE = 'annulee';
}
