<?php
namespace Watchlist\Controllers;

use Watchlist\Models\User;

/**
 * User Controller
 *
 * Controller handling actions related to users:
 * - register
 * - login
 * - profile info
 */
class UserController extends \Phalcon\Mvc\Controller
{
	//TODO: move to a parent controller
	public function redirect(string $url) // should I allow for a message here? What then about message / error / warning?
	{
		//TODO: if $url is an array, $url = $this->url->get($url)?
		$this->response->redirect($url, true);
		$this->view->disable();
		return;
	}

	public function indexAction()
	{
		if (empty($this->session->getUser())) {
			$this->flash->error('Not logged in!');
			return $this->redirect($this->url->get(['for' => 'top']));
		}
	}

	public function registerAction()
	{
		if (!empty($this->session->getUser())) {
			$this->flash->error('Already logged in!');
			return $this->redirect($this->url->get(['for' => 'top']));
		}

		if ($this->request->isPost()) {
			//TODO: validate fields
			$password = $this->request->getPost('password');
			$user = new User();
			$user->email = $this->request->getPost('email');
			$user->name = $this->request->getPost('name');
			$user->password = $this->security->hash($password);
			$user->save();

			$this->session->login($user);

			$this->flash->success('Welcome ' . $user->name);
			return $this->redirect($this->url->get(['for' => 'user home']));
		}
	}

	public function loginAction()
	{
		if (!empty($this->session->getUser())) {
			$this->flash->error('Already logged in!');
			return $this->redirect($this->url->get(['for' => 'top']));
		}

		if ($this->request->isPost()) {
			$email	= $this->request->getPost('email');
			$password = $this->request->getPost('password');

			$user = User::findFirst([['email' => $email]]);

			if (!empty($user) && $this->security->checkHash($password, $user->password)) {
				$this->session->login($user);

				$this->flash->success('Welcome back ' . $user->name);
				$this->redirect($this->url->get(['for' => 'user home']));
			}

			// to protect against timing attacks we might want to $this->security->hash(rand()); when no user is found

			$this->flash->error('Wrong email/password');
		}
	}

	public function logoutAction()
	{
		$this->session->logout();
		// then show "logged out" message.
	}

	public function profileAction()
	{
		if ($this->request->getQuery('user')) {
			return $this->dispatcher->forward(['controller' => 'user', 'action' => 'showProfile']);
		}

		if (!empty($this->session->getUser())) {
			return $this->dispatcher->forward(['controller' => 'user', 'action' => 'editProfile']);
		}

		$this->flash->error('No user specified!');
		return $this->dispatcher->forward(['controller' => 'index', 'action' => 'index']);
	}

	public function showProfileAction()
	{
		$this->view->user = User::findFirst([['id' => $this->request->getQuery('id')]]);
	}

	public function editProfileAction()
	{
		$this->view->user = $this->session->getUser();
	}
}

