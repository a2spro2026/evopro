<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class WhatsApp
{
    public static function defaults(): array
    {
        return [
            'actif' => true,
            'mode' => 'lien',
            'indicatif' => '212',
            'numero_business' => '',
            'access_token' => '',
            'phone_number_id' => '',
            'messages_actifs' => true,
            'appels_actifs' => true,
            'message_defaut' => 'Bonjour, je vous contacte concernant votre projet.',
            'template_name' => '',
            'template_lang' => 'fr',
        ];
    }

    public static function config(): array
    {
        return array_merge(self::defaults(), AppStore::getConfig('whatsapp'));
    }

    public static function isApiReady(?array $cfg = null): bool
    {
        $cfg ??= self::config();

        return ($cfg['actif'] ?? false)
            && ($cfg['messages_actifs'] ?? false)
            && ($cfg['mode'] ?? '') === 'api'
            && trim((string) ($cfg['access_token'] ?? '')) !== ''
            && trim((string) ($cfg['phone_number_id'] ?? '')) !== '';
    }

    public static function normalizePhone(string $telephone, ?string $indicatif = null): string
    {
        $digits = preg_replace('/\D+/', '', $telephone) ?? '';
        if ($digits === '') {
            return '';
        }

        $indicatif = preg_replace('/\D+/', '', $indicatif ?? self::config()['indicatif'] ?? '212') ?: '212';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0') && strlen($digits) >= 9) {
            $digits = $indicatif.substr($digits, 1);
        }

        return $digits;
    }

    public static function chatUrl(string $telephone, string $message = '', ?string $indicatif = null): ?string
    {
        $phone = self::normalizePhone($telephone, $indicatif);
        if ($phone === '') {
            return null;
        }

        $url = 'https://wa.me/'.$phone;
        $message = trim($message);
        if ($message !== '') {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    public static function callUrl(string $telephone, ?string $indicatif = null): ?string
    {
        $phone = self::normalizePhone($telephone, $indicatif);
        if ($phone === '') {
            return null;
        }

        return 'https://wa.me/'.$phone;
    }

    /**
     * @return array{ok: bool, message: string, url?: string|null}
     */
    public static function sendMessage(string $telephone, string $message): array
    {
        $cfg = self::config();

        if (! ($cfg['actif'] ?? false)) {
            return ['ok' => false, 'message' => 'WhatsApp est désactivé dans la configuration.'];
        }

        if (! ($cfg['messages_actifs'] ?? false)) {
            return ['ok' => false, 'message' => 'L’envoi de messages WhatsApp est désactivé.'];
        }

        $phone = self::normalizePhone($telephone, $cfg['indicatif'] ?? '212');
        if ($phone === '') {
            return ['ok' => false, 'message' => 'Numéro de téléphone invalide.'];
        }

        $message = trim($message);
        if ($message === '') {
            $message = (string) ($cfg['message_defaut'] ?? '');
        }

        if (($cfg['mode'] ?? 'lien') !== 'api') {
            return [
                'ok' => true,
                'message' => 'Lien WhatsApp prêt.',
                'url' => self::chatUrl($telephone, $message, $cfg['indicatif'] ?? '212'),
            ];
        }

        $token = trim((string) ($cfg['access_token'] ?? ''));
        $phoneNumberId = trim((string) ($cfg['phone_number_id'] ?? ''));

        if ($token === '' || $phoneNumberId === '') {
            return [
                'ok' => false,
                'message' => 'Pour envoyer automatiquement, collez le jeton et le Phone Number ID dans Configuration → WhatsApp.',
            ];
        }

        $templateName = trim((string) ($cfg['template_name'] ?? ''));
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
        ];

        if ($templateName !== '') {
            $payload['type'] = 'template';
            $payload['template'] = [
                'name' => $templateName,
                'language' => [
                    'code' => trim((string) ($cfg['template_lang'] ?? 'fr')) ?: 'fr',
                ],
            ];
        } else {
            $payload['type'] = 'text';
            $payload['text'] = [
                'preview_url' => false,
                'body' => $message !== '' ? $message : 'Bonjour',
            ];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(25)
                ->post('https://graph.facebook.com/v21.0/'.$phoneNumberId.'/messages', $payload);

            if (! $response->successful()) {
                $error = data_get($response->json(), 'error.message', 'Échec de l’envoi via l’API Meta.');

                return ['ok' => false, 'message' => (string) $error];
            }

            return ['ok' => true, 'message' => 'Message WhatsApp envoyé au client.', 'url' => null];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Erreur réseau WhatsApp : '.$e->getMessage()];
        }
    }

    /**
     * Envoie un PDF directement au client via l’API WhatsApp Cloud.
     *
     * @return array{ok: bool, message: string, url?: string|null, needs_api?: bool}
     */
    public static function sendDocument(string $telephone, string $absolutePath, string $filename, string $caption = ''): array
    {
        $cfg = self::config();

        if (! ($cfg['actif'] ?? false)) {
            return ['ok' => false, 'message' => 'WhatsApp est désactivé dans la configuration.'];
        }

        if (! ($cfg['messages_actifs'] ?? false)) {
            return ['ok' => false, 'message' => 'L’envoi de messages WhatsApp est désactivé.'];
        }

        $phone = self::normalizePhone($telephone, $cfg['indicatif'] ?? '212');
        if ($phone === '') {
            return ['ok' => false, 'message' => 'Numéro de téléphone invalide.'];
        }

        if (! is_file($absolutePath)) {
            return ['ok' => false, 'message' => 'Fichier PDF introuvable.'];
        }

        $token = trim((string) ($cfg['access_token'] ?? ''));
        $phoneNumberId = trim((string) ($cfg['phone_number_id'] ?? ''));

        if ($token === '' || $phoneNumberId === '') {
            return [
                'ok' => false,
                'message' => 'Envoi automatique non configuré. Allez dans Configuration → WhatsApp et collez le Token + Phone Number ID.',
                'needs_api' => true,
            ];
        }

        $filename = trim($filename) !== '' ? $filename : 'devis-evopro.pdf';
        if (! str_ends_with(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }

        try {
            $upload = Http::withToken($token)
                ->acceptJson()
                ->timeout(60)
                ->attach('file', file_get_contents($absolutePath), $filename, ['Content-Type' => 'application/pdf'])
                ->post('https://graph.facebook.com/v21.0/'.$phoneNumberId.'/media', [
                    'messaging_product' => 'whatsapp',
                    'type' => 'application/pdf',
                ]);

            if (! $upload->successful()) {
                $error = data_get($upload->json(), 'error.message', 'Échec de l’upload du PDF vers WhatsApp.');

                return ['ok' => false, 'message' => (string) $error];
            }

            $mediaId = (string) data_get($upload->json(), 'id', '');
            if ($mediaId === '') {
                return ['ok' => false, 'message' => 'WhatsApp n’a pas renvoyé d’identifiant média.'];
            }

            $document = [
                'id' => $mediaId,
                'filename' => $filename,
            ];
            $caption = trim($caption);
            if ($caption !== '') {
                $document['caption'] = mb_substr($caption, 0, 1024);
            }

            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(30)
                ->post('https://graph.facebook.com/v21.0/'.$phoneNumberId.'/messages', [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'document',
                    'document' => $document,
                ]);

            if (! $response->successful()) {
                $error = data_get($response->json(), 'error.message', 'Échec de l’envoi du PDF via WhatsApp.');

                return ['ok' => false, 'message' => (string) $error];
            }

            return ['ok' => true, 'message' => 'Devis PDF envoyé au client sur WhatsApp.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Erreur réseau WhatsApp : '.$e->getMessage()];
        }
    }

    public static function hasApiCredentials(?array $cfg = null): bool
    {
        $cfg ??= self::config();

        return trim((string) ($cfg['access_token'] ?? '')) !== ''
            && trim((string) ($cfg['phone_number_id'] ?? '')) !== '';
    }
}
