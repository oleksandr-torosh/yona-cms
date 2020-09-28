<?php
/**
 * @author Alexander Torosh <webtorua@gmail.com>
 */

namespace Api;

use Api\Controllers\AuthController;
use Api\Controllers\IndexController;
use Api\Controllers\UsersController;
use Api\Exception\NotFoundException;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection as MicroCollection;

class Router
{
    /**
     * @param Micro $app
     */
    public function init(Micro $app)
    {
        // Not Found
        $app = $this->handleNotFound($app);

        // Mount Routes
        $app->mount($this->index());
        $app->mount($this->auth());
        $app->mount($this->users());
    }

    /**
     * @param Micro $app
     * @return Micro
     */
    private function handleNotFound(Micro $app): Micro
    {
        // Not Found
        $app->notFound(
            function () use ($app) {
                throw new NotFoundException('Page Not Found', 404);
            }
        );

        return $app;
    }

    /**
     * @return MicroCollection
     */
    private function index(): MicroCollection
    {
        $collection = new MicroCollection();
        $collection->setHandler(IndexController::class, true);
        $collection->setPrefix('/api');

        $collection->get('', 'index');

        return $collection;
    }

    /**
     * @return MicroCollection
     */
    private function auth(): MicroCollection
    {
        $collection = new MicroCollection();
        $collection->setHandler(AuthController::class, true);
        $collection->setPrefix('/api/auth');

        $collection->post('', 'authenticate');
        $collection->post('/csrf', 'createCsrfToken');

        return $collection;
    }

    /**
     * @return MicroCollection
     */
    private function users(): MicroCollection
    {
        $collection = new MicroCollection();
        $collection->setHandler(UsersController::class, true);
        $collection->setPrefix('/api/users');

        $collection->get('', 'usersList');
        $collection->post('', 'create');
        $collection->get('/{userID:\d+}', 'retrieveById');

        return $collection;
    }
}
