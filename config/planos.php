<?php

return [

    'demo' => [
        'nome'               => 'Demo',
        'limite_usuarios'    => 3,
        'limite_processos'   => 20,
        'limite_storage_mb'  => 50,
        'trial_dias'         => 15,
        'ia_habilitada'      => false,
        'datajud_habilitado' => false,
        'whatsapp_habilitado'=> false,
    ],

    'basico' => [
        'nome'               => 'Básico',
        'limite_usuarios'    => 5,
        'limite_processos'   => 200,
        'limite_storage_mb'  => 500,
        'ia_habilitada'      => false,
        'datajud_habilitado' => true,
        'whatsapp_habilitado'=> true,
    ],

    'pro' => [
        'nome'               => 'Pro',
        'limite_usuarios'    => 15,
        'limite_processos'   => 1000,
        'limite_storage_mb'  => 2048,
        'ia_habilitada'      => true,
        'datajud_habilitado' => true,
        'whatsapp_habilitado'=> true,
    ],

    'enterprise' => [
        'nome'               => 'Enterprise',
        'limite_usuarios'    => 999,
        'limite_processos'   => 999999,
        'limite_storage_mb'  => 10240,
        'ia_habilitada'      => true,
        'datajud_habilitado' => true,
        'whatsapp_habilitado'=> true,
    ],

    // Mantém compatibilidade com o plano 'starter' existente
    'starter' => [
        'nome'               => 'Starter',
        'limite_usuarios'    => 5,
        'limite_processos'   => 50,
        'limite_storage_mb'  => 200,
        'ia_habilitada'      => true,
        'datajud_habilitado' => true,
        'whatsapp_habilitado'=> true,
    ],

];
