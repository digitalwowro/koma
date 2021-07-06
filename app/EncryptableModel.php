<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class EncryptableModel extends Model
{
    protected $decryptedData;

    abstract public function isOwner(User $user);

    /**
     * Decode the data field
     *
     * @return array
     * @throws \App\Exceptions\InvalidResourceException
     */
    public function getDataAttribute() : array
    {
        if (is_null($this->decryptedData)) {
            $this->decryptedData = EncryptedStore::pull($this);
        }

        return $this->decryptedData;
    }

    //public function hasStore()
    //{
    //    if (is_null($this->decryptedData)) {
    //        $this->decryptedData = EncryptedStore::pull($this, true);
    //    }
    //
    //    return true;
    //}

    /**
     * @param string $key
     * @return bool
     */
    private function keyIsEncrypted($key) : bool
    {
        $fields = $this->encryptable ?? [];

        return is_array($fields) && in_array($key, $fields);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (!$this->keyIsEncrypted($key)) {
            return parent::__get($key);
        }

        $value = $this->data[$key] ?? null;

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if (!$this->keyIsEncrypted($key)) {
            return parent::setAttribute($key, $value);
        }

        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        }

        $data = $this->data;
        $data[$key] = $value;
        $this->decryptedData = $data;
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        if (!$this->keyIsEncrypted($key)) {
            return parent::__isset($key);
        }

        return isset($this->data[$key]);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        if (!$this->keyIsEncrypted($key)) {
            return parent::__unset($key);
        }

        $data = $this->data;
        unset($data[$key]);
        $this->decryptedData = $data;
    }

    /**
     * Save the encrypted data to the database.
     *
     * @param array $options
     * @return bool
     * @throws Exceptions\InvalidResourceException
     */
    public function save(array $options = []) {
        parent::save($options);

        EncryptedStore::upsert($this, $this->decryptedData);
    }
}
