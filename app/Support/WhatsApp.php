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
        ];
    }

    public static function config(): array
    {
        return array_merge(self::defaults(), AppStore::getConfig('whatsapp'));
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

        // Ouvre WhatsApp sur le contact (appel possible depuis l'app).
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
            return ['ok' => false, 'message' => 'Configurez le jeton d’accès et le Phone Number ID Meta.'];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post('https://graph.facebook.com/v21.0/'.$phoneNumberId.'/messages', [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message !== '' ? $message : 'Bonjour',
                    ],
                ]);

            if (! $response->successful()) {
                $error = data_get($response->json(), 'error.message', 'Échec de l’envoi via l’API Meta.');

                return ['ok' => false, 'message' => (string) $error];
            }

            return ['ok' => true, 'message' => 'Message WhatsApp envoyé.', 'url' => null];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Erreur réseau WhatsApp : '.$e->getMessage()];
        }
    }
}
