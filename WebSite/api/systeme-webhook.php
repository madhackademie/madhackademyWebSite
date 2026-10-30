<?php
declare(strict_types=1);

/**
 * Webhook Systeme.io — validation d’achat (TVA / facturation côté Systeme.io).
 * Événement abonné : SALE_NEW → grant deck sur madhackademy.eu
 *
 * URL à configurer : Paramètres Systeme.io → Webhooks → https://madhackademy.eu/api/systeme-webhook.php
 */
require_once __DIR__ . '/bootstrap.php';

mha_systeme_webhook_serve();
