<?php

namespace App\Enum;

enum UserRole: string
{
    case UTILISATEUR = 'utilisateur';
    case ADMIN = 'admin';
    case EMPLOYE = 'employe';
}