<?php

namespace App\Models\MercadoLibre;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CbtApiToken
 * @package App\Models\MercadoLibre
 *
 * @property string $access_token
 * @property string $refresh_token
 */
class CbtApiToken extends Model
{
    public $timestamps = false;

    public $dates = ['acquired_at', 'expires_at'];

    protected $table = 'mercadolibre_cbt_api_tokens';

    protected $fillable = ['access_token', 'refresh_token', 'acquired_at', 'expires_at'];
}
