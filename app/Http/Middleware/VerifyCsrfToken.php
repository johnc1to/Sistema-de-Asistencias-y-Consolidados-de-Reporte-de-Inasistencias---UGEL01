<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = ['tituladosCetpro','viewTitulo','consulta-titulo-aprobado','reporte','consulta_app_modulos','consulta_especialistas'];
}
