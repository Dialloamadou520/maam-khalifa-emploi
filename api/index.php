<?php

/**
 * Point d'entrée des fonctions serverless Vercel : le système de fichiers y est
 * en lecture seule, seul /tmp est inscriptible. Les dossiers de travail de
 * Laravel doivent donc exister avant le démarrage du framework.
 */
foreach ([
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
] as $dossier) {
    if (! is_dir($dossier)) {
        mkdir($dossier, 0755, true);
    }
}

require __DIR__.'/../public/index.php';
