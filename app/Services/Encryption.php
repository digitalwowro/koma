<?php

namespace App\Services;

use App\User;
use Exception;
use ParagonIE\Halite\Asymmetric\EncryptionPublicKey;
use ParagonIE\Halite\EncryptionKeyPair;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use Illuminate\Http\Request;
use ParagonIE\Halite\Asymmetric\Crypto as Asymmetric;

class Encryption
{
    private $keyPair;

    /**
     * @return string
     */
    public function securityLevel()
    {
        return config('koma.encryption.level');
    }

    /**
     * @param Request|null $request
     * @return EncryptionKeyPair
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function getKeyPair(Request $request = null) : EncryptionKeyPair
    {
        if (!$this->keyPair) {
            $request = $request ?: request();

            if (!$request instanceof Request) {
                throw new Exception('Invalid request');
            }

            if (!$request->hasCookie('key')) {
                throw new Exception('Missing key. Are cookies disabled in your browser?');
            }

            $password = new HiddenString($request->cookie('key'));
            $salt = base64_decode($request->user()->salt);
            $security = $this->securityLevel();

            $this->keyPair = KeyFactory::deriveEncryptionKeyPair($password, $salt, $security);
        }

        return $this->keyPair;
    }

    public function encrypt($str)
    {
        $str = new HiddenString($str);

        return Asymmetric::seal($str, $this->getKeyPair()->getPublicKey());
    }

    public function encryptForUser($str, User $user)
    {
        $str = new HiddenString($str);
        $publicKey = new EncryptionPublicKey(new HiddenString(base64_decode($user->public_key)));

        return Asymmetric::seal($str, $publicKey);
    }

    public function decrypt($str)
    {
        return Asymmetric::unseal($str, $this->getKeyPair()->getSecretKey())->getString();
    }
}
