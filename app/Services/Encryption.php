<?php

namespace App\Services;

use App\EncryptedStore;
use App\User;
use Exception;
use ParagonIE\Halite\Asymmetric\EncryptionPublicKey;
use ParagonIE\Halite\EncryptionKeyPair;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use Illuminate\Http\Request;
use ParagonIE\Halite\Asymmetric\Crypto as Asymmetric;
use ParagonIE\Halite\Symmetric\Crypto as Symmetric;

class Encryption
{
    private $keyPair;
    private $throwException = true;

    /**
     * Disable throwing exception on decryption failure
     */
    public function disableExceptions()
    {
        $this->throwException = false;
    }

    /**
     * Enable throwing exception on decryption failure
     */
    public function enableExceptions()
    {
        $this->throwException = true;
    }

    /**
     * Returns whether exception throwing is enabled
     *
     * @return bool
     */
    public function getExceptions()
    {
        return $this->throwException;
    }

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
     * @throws Exception
     */
    public function getKeyPair(Request $request = null) : EncryptionKeyPair
    {
        if (!$this->keyPair) {
            $request = $request ?: request();

            if (!$request->hasCookie('key')) {
                throw new Exception('Missing key. Are cookies disabled in your browser?');
            }

            $this->setKeyPair($request->user(), $request->cookie('key'));
        }

        return $this->keyPair;
    }

    /**
     * @param User $user
     * @param string $password
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function setKeyPair(User $user, string $password)
    {
        $password = new HiddenString($password);
        $salt = base64_decode($user->salt);
        $security = $this->securityLevel();

        $this->keyPair = KeyFactory::deriveEncryptionKeyPair($password, $salt, $security);
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

    /**
     * Decrypt String
     *
     * @param string $str
     * @return bool|string
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function decrypt($str)
    {
        if ($this->throwException) {
            return Asymmetric::unseal($str, $this->getKeyPair()->getSecretKey())->getString();
        }

        try {
            return Asymmetric::unseal($str, $this->getKeyPair()->getSecretKey())->getString();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate new salt & encryption key
     *
     * @param string $password
     * @return array
     * @throws Exception
     */
    public function generateEncryptionKey(string $password) : array
    {
        $salt = random_bytes(16);

        $password = new HiddenString($password);
        $security = $this->securityLevel();

        $publicKey = KeyFactory::deriveEncryptionKeyPair($password, $salt, $security)
            ->getPublicKey()
            ->getRawKeyMaterial();

        $salt = base64_encode($salt);
        $publicKey = base64_encode($publicKey);

        return compact('salt', 'publicKey');
    }

    /**
     * Re-encrypt entire encrypted store for current
     * logged in user using a new provided password
     *
     * @param string $password
     * @param User $user
     * @return string
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function changePassword(string $password, User $user) : string
    {
        $salt = base64_decode($user->salt);
        $password = new HiddenString($password);
        $security = $this->securityLevel();

        $newKeyPair = KeyFactory::deriveEncryptionKeyPair($password, $salt, $security);
        $publicKey = $newKeyPair->getPublicKey();

        EncryptedStore::where('user_id', $user->id)
            ->chunk(200, function($items) use ($password, $publicKey) {
                foreach ($items as $item) {
                    try {
                        $data = new HiddenString($this->decrypt($item->data));

                        $item->data = Asymmetric::seal($data, $publicKey);
                        $item->save();
                    } catch (Exception $e) {
                        // skip
                    }
                }
            });

        return base64_encode($publicKey->getRawKeyMaterial());
    }

    /**
     * Encrypt password using recovery string
     *
     * @param string $recoveryString
     * @param string $password
     * @param User|string $userSalt
     * @return string
     * @throws \Exception
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function recoveryString(string $recoveryString, string $password, $userSalt) : string
    {
        if ($userSalt instanceof User) {
            $salt = base64_decode($userSalt->salt);
        } elseif (is_string($userSalt)) {
            $salt = $userSalt;
        } else {
            throw new Exception('Invalid user salt');
        }

        $key = new HiddenString($recoveryString);
        $password = new HiddenString($password);
        $encryptionKey = KeyFactory::deriveEncryptionKey($key, $salt);

        return Symmetric::encrypt($password, $encryptionKey);
    }

    /**
     * @param string $recoveryString
     * @param User $user
     * @return string
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidSignature
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function recoverPassword(string $recoveryString, User $user) : string
    {
        $recoveryString = preg_replace('/[^A-Za-z0-9]/', '', $recoveryString);
        $salt = base64_decode($user->salt);
        $key = new HiddenString($recoveryString);
        $encryptionKey = KeyFactory::deriveEncryptionKey($key, $salt);

        return Symmetric::decrypt($user->recovery_string, $encryptionKey);
    }
}
