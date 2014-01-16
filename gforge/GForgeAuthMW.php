<?php

require_once ('../../../www/env.inc.php');
require_once $gfcommon.'include/ProjectManager.class.php';
require_once $gfcommon.'include/pre.php';

class GForgeAuthMW extends \Slim\Middleware
{
    protected $cfg;
    protected $userMgr;
    protected $gforge;

    function __construct($cfg) {
        $this->cfg = $cfg;
        //require_once "GForgeApiFake.php";
        //$this->gforge = new GForgeApiFake();
        require_once "GForgeApi.php";
        $this->gforge = new GForgeApi();
    }
    
    public function call()
    {
        $app = $this->app;

        $this->userMgr = new UserManager($app->db); 
        $app->userMgr = $this->userMgr;

        $gforgeUserInfo = $this->gforge->getGForgeUserInfo();
        if ($gforgeUserInfo) {
            // create user, unless it exists
            $app->userMgr->setAndCreateUserIfNotExists('gforge', 
                                                       $gforgeUserInfo['username'], 
                                                       $gforgeUserInfo['email'], 
                                                       $gforgeUserInfo['displayname'], 
                                                       $gforgeUserInfo['image_url']);

            $app->userInfo = $app->userMgr->getUserInfo();

            // seht the current user id for the backlog api
            $app->backlog->setUserId($app->userInfo['id']);
        }
        
        // Run inner middleware and application
        $this->next->call();
    }

    function setProjectName($projectName) {
        $projectInfo = $this->userMgr->getProjectInfo($projectName);

        // ensure, that a gforge project is also available within backle
        // TODO: handle updates of the project
        if (! $projectInfo) {            
            $gforgeProjectInfo = $this->gforge->getGForgeProjectInformation($projectName);
            if ($gforgeProjectInfo) {
                
                $projectId = $this->userMgr->createProject($projectName, 
                                              $gforgeProjectInfo['title'], 
                                              $gforgeProjectInfo['is_public_viewable']);
                
                // create default backlog
                $backlogId = $this->app->backlog->createBacklog($projectName, $gforgeProjectInfo['title'], $gforgeProjectInfo['is_public_viewable'], $projectId, true);
                $projectInfo = $this->userMgr->getProjectInfo($projectName);
            } else {
                // project not found within gforge!
            }
        }

        if ($this->app->userInfo) {

            // update project rights on demand
            $gforgeProjectRights = $this->gforge->getGForgeProjectRights($projectName);
            $this->userMgr->setUserProjectRights($projectInfo['id'], $this->app->userInfo['id'], $gforgeProjectRights);
        }
    }
}