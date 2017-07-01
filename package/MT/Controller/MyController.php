<?php

namespace MT\Controller;

use Zend\Mvc\MvcEvent,
    Zend\Mvc\Controller\AbstractActionController,
    My\General,
    MT\StaticManager;

class MyController extends AbstractActionController
{
    protected $defaultJS = '';
    protected $externalJS = '';
    protected $defaultCSS = '';
    protected $externalCSS = '';
    protected $params;
    protected $serverUrl;
    private $resource;
    private $renderer;

    /*
     *@params array $params
     */
    protected function setParams($params){
        $this->params = $params;
    }

    /*
     *@return array $params
     */
    protected function getParams(){
        return $this->params;
    }

    /*
     *@params array $resource
     */
    protected function setResource($resource){
        $this->resource = $resource;
    }

    /*
     *@return string $resource
     */
    protected function getResource(){
        return $this->resource;
    }

    /*
     *@params array $renderer
     */
    protected function setRenderer($render){
        $this->renderer = $render;
    }

    /*
     *@return array $renderer
     */
    protected function getRenderer(){
        return $this->renderer;
    }

    public function onDispatch(MvcEvent $e)
    {
        if (php_sapi_name() != 'cli') {
            $this->serverUrl = $this->getRequest()->getUri()->getScheme() . '://' . $this->getRequest()->getUri()->getHost();
            $params = array_merge($this->params()->fromRoute(), $this->params()->fromQuery());
            $arr_controller = explode('\\', $params['controller']);
            $this->setParams([
                'module' => strtolower(current($arr_controller)),
                'controller' => strtolower(end($arr_controller)),
                'action' => strtolower($params['action'])
            ]);
            $this->setResource($this->params['module'] . ':' . $this->params['controller'] . ':' . $this->params['action']);
            $this->setRenderer($this->serviceLocator->get('Zend\View\Renderer\PhpRenderer'));
            $auth = $this->authenticate();

            if ($this->params['module'] === 'admin' && !$auth) {
                if (!$this->permission($this->params)) {
                    if ($this->request->isXmlHttpRequest()) {
                        die('Permission Denied!!!');
                    }
                    $this->layout('admin/error/accessDeny');
                    return false;
                }
            }

            $instanceStaticManager = new StaticManager\StaticManager($this->resource, $this->serviceLocator);
            $instanceStaticManager
                ->setJS(array('defaultJS' => $this->defaultJS))
                ->setJS(array('externalJS' => $this->externalJS))
                ->setCSS(array('defaultCSS' => $this->defaultCSS))
                ->setCSS(array('externalCSS' => $this->externalCSS))
                ->render(2.1);
            $this->setMeta($this->params);
        }
        return parent::onDispatch($e);
    }

    private function setMeta($arrData)
    {
        switch ($this->resource) {
            case 'frontend:index:index':
                $this->renderer->headTitle(\My\General::SITE_DOMAIN . ' - ' . General::SITE_SLOGAN);
                $this->renderer->headMeta()->setProperty('url', \My\General::SITE_DOMAIN_FULL);
                $this->renderer->headMeta()->setProperty('og:url', General::SITE_DOMAIN_FULL);
                $this->renderer->headMeta()->appendName('title', html_entity_decode(\My\General::SITE_DOMAIN . ' -' . General::SITE_SLOGAN));
                $this->renderer->headMeta()->setProperty('og:title', html_entity_decode(\My\General::SITE_DOMAIN . ' -' . General::SITE_SLOGAN));
                $this->renderer->headMeta()->setProperty('og:description', html_entity_decode(General::DESCRIPTION_DEFAULT));
                $this->renderer->headMeta()->appendName('keywords', General::KEYWORD_DEFAULT);
                $this->renderer->headMeta()->appendName('description', General::DESCRIPTION_DEFAULT);
                $this->renderer->headMeta()->appendName('image', General::SITE_IMAGES_DEFAULT);
                $this->renderer->headMeta()->setProperty('og:image', General::SITE_IMAGES_DEFAULT);
                break;
            default:
                break;
        }
        if ($arrData['module'] === 'backend') {
            $this->renderer->headTitle('Administrator - ' . General::SITE_AUTH);
        }
    }

    private function permission($params)
    {

        //check can access CPanel
        if (IS_ACP != 1) {
            return false;
        }

        //check use in fullaccess role
        if (FULL_ACCESS) {
            return true;
        }

        $ser = $this->serviceLocator;
        $serviceACL = $this->serviceLocator->get('ACL');

        $strActionName = $params['action'];

        if (strpos($params['action'], '-')) {
            $strActionName = '';
            $arrActionName = explode('-', $params['action']);
            foreach ($arrActionName as $k => $str) {
                if ($k > 0) {
                    $strActionName .= ucfirst($str);
                }
            }
            $strActionName = $arrActionName[0] . $strActionName;
        }

        $strControllerName = $params['controller'];
        if (strpos($params['controller'], '-')) {
            $strControllerName = '';
            $arrControllerName = explode('-', $params['controller']);
            foreach ($arrControllerName as $k => $str) {
                if ($k > 0) {
                    $strControllerName .= ucfirst($str);
                }
            }
            $strControllerName = $arrControllerName[0] . $strControllerName;
        }

        $strActionName = str_replace('_', '', $strActionName);
        $strControllerName = str_replace('_', '', $strControllerName);

        return $serviceACL->checkPermission($params['module'], $strControllerName, $strActionName);
    }

    protected function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }

    private function authenticate()
    {
        $arrData = $this->getParams();
//        $arrUserData = $this->getAuthService()->getIdentity();
//        if ($arrData['module'] === 'backend') {
//
//            if (empty($arrUserData)) {
//                return $this->redirect()->toRoute('backend', array('controller' => 'auth', 'action' => 'login'));
//            }
//
//            define('UID', (int)$arrUserData['user_id']);
//            define('MODULE', $arrData['module']);
//            define('CONTROLLER', $arrData['controller']);
//            define('FULLNAME', $arrUserData['user_fullname']);
//            define('USERNAME', $arrUserData['user_name']);
//            define('EMAIL', $arrUserData['user_email']);
//            define('GROU_ID', $arrUserData['group_id'] ? (int)$arrUserData['group_id'] : 0);
//            define('IS_ACP', (empty($arrUserData['group_id']) ? 0 : $arrUserData['is_acp']));
//            define('PERMISSION', json_encode($arrUserData['permission']));
//            define('FULL_ACCESS', empty($arrUserData['is_full_access']) ? 0 : 1);
//        }

        if ($arrData['module'] === 'index') {

//            define('ARR_CONTENT_HOT_LIST', serialize($arr_content_hot));
//
//            define('KEYWORD_SEARCH', !empty($arrData['keyword']) && $arrData['action'] == 'index' && $arrData['controller'] == 'search' ? $arrData['keyword'] : NULL);
        }
    }

}
