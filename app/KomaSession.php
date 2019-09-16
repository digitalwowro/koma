<?php

namespace App;

use Illuminate\Database\QueryException;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class KomaSession extends DatabaseSessionHandler
{
    protected function getLifetimeFromRequest()
    {
        switch (request()->input('session')) {
            case '1h':
                return 60;
            case '1d':
                return 24 * 60;
            case '3d':
                return 3 * 24 * 60;
            case '7d':
                return 7 * 24 * 60;
        }

        return null;
    }

    protected function performInsert($sessionId, $payload)
    {
        $lifetime = $this->getLifetimeFromRequest();

        try {
            $payload = Arr::set($payload, 'id', $sessionId);

            $insertPayload = $payload;

            if (!empty($lifetime)) {
                $insertPayload['expires_at'] = Carbon::now()->addMinutes($lifetime);
            }

            return $this->getQuery()->insert($insertPayload);
        } catch (QueryException $e) {
            $this->performUpdate($sessionId, $payload);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        $this->getQuery()
            ->where('last_activity', '<=', $this->currentTime() - $lifetime)
            ->where('expires_at', '<', Carbon::now())
            ->delete();
    }

    /**
     * Determine if the session is expired.
     *
     * @param  \stdClass  $session
     * @return bool
     */
    protected function expired($session)
    {
        if (isset($session->expires_at) && $session->expires_at > Carbon::now()) {
            return false;
        }

        return isset($session->last_activity) &&
            $session->last_activity < Carbon::now()->subMinutes($this->minutes)->getTimestamp();
    }
}
