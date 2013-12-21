<?php

namespace GooglePosta\MVC;

use Config\Config;
use GooglePosta\MVC\Base\Controller;
use GooglePosta\MVC\Base\View;
use GooglePosta\MVC\Model\Authority as AuthorityModel;
use Path\Resolver;
use Web\Response\Status;
use Web\Web;

class Authority extends Controller
{
    /**
     * @var AuthorityModel
     */
    protected $model;

    /**
     * @param Web            $web
     * @param AuthorityModel $model
     * @param View           $view
     * @param Config         $config
     * @param Resolver       $pathResolver
     */
    function __construct(
        Web $web,
        AuthorityModel $model,
        View $view,
        Config $config,
        Resolver $pathResolver
    ) {
        parent::__construct($web, $model, $view, $config, $pathResolver);
    }

    /**
     * Run the controller
     *
     * @param array $params
     *
     * @throws \Web\Exception\RuntimeException
     */
    public function run($params = array())
    {
        $requestMethod  = strtoupper($this->request->server('REQUEST_METHOD'));
        $action         = '';

        if (isset($params['action'])) {
            $action = strtoupper($params['action']);
        }

        if (!is_empty($this->request->get('code'))) {
            $this->confirmAuthority();
        }
        elseif ($requestMethod === 'DELETE' || $action === 'DELETE') {
            $this->purgeAuthority();
        }
        else {
            $this->initAuthority();
        }

        $this->respond(Status::OK);
    }

    /**
     * @throws \Web\Exception\RuntimeException
     * @return Authority
     */
    protected function purgeAuthority()
    {
        $this->model->purgeAuthority(
            $this->request->post('email'),
            $this->request->post('lapostaApiToken')
        );

        $returnUrl = $this->request->post('returnUrl');

        if (!empty($returnUrl)) {
            $this->redirect($returnUrl);
        }

        return $this;
    }

    /**
     * @return Authority
     * @throws \Web\Exception\RuntimeException
     */
    protected function initAuthority()
    {
        $redirect = $this->model->initiate(
            $this->request->post('email'),
            $this->request->post('lapostaApiToken'),
            $this->request->post('returnUrl')
        );

        if (!empty($redirect)) {
            $this->redirect($redirect);
        }

        return $this;
    }

    /**
     * @return Authority
     */
    protected function confirmAuthority()
    {
        $this->model->confirmAuthority($this->request->get('code'));

        $redirect = $this->model->getClientReturnUrl();

        if (!empty($redirect)) {
            $this->redirect($redirect);
        }

        return $this;
    }
}


