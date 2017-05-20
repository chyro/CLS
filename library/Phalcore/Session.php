<?php

namespace Phalcore;

use \Watchlist\Models\User;
use \Phalcon\Session\AdapterInterface as SessionEngine;

/**
 * Session class with extra convenience features
 * (currently only user login)
 * Session class passed on build, rather than extended from, so the choice
 * of session storage is done in the bootstrap rather than in the Phalcore
 * code.
 * (This should probably implement AdapterInterface, though.)
 */
class Session {
    /**
     * @var SessionEngine
     */
    private $session;

    public function __construct(SessionEngine $session)
    {
        $this->session = $session;
    }

    public function getUser() //: ?User //PHP7.1
    {
        if ($this->session->has('auth')) {
            $userSession = $this->session->get('auth');
            //maybe: cache user?
            return User::findFirst([['id' => $userSession['id']]]);
        }
    }

    public function login(User $user)
    {
        $this->session->set('auth', ['id' => $user->id, 'name' => $user->name]);
        // store a DBRef maybe? Phalcore should not have dependencies to Watchlist... Maybe Phalcore needs a Models\User and Watchlist's would extend that?
    }

    public function logout()
    {
        $this->session->remove('auth');
        $this->session->destroy();
    }

    //Replace those functions with a __call?
    public function get(string $key)
    {
        return $this->session->get($key);
    }
    public function set(string $key, $val)
    {
        return $this->session->set($key, $val);
    }
    public function has(string $key)
    {
        return $this->session->has($key);
    }
    public function start()
    {
        return $this->session->start();
    }
    public function destroy()
    {
        return $this->session->destroy();
    }
    public function remove(string $key)
    {
        return $this->session->remove($key);
    }
}

