<?php

namespace App\Models;

class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken
{
    protected $table = 'API_personal_access_tokens';
    protected $connection = 'mysql-pompeyo';

}
