<?php

class GForgeAuthMW extends \Slim\Middleware
{
    protected $cfg;
    protected $userMgr;

    function __construct($cfg) {
        $this->cfg = $cfg;
    }
    
    public function call()
    {
        $app = $this->app;

        $this->userMgr = new UserManager($app->db); 
        $app->userMgr = $this->userMgr;

        $gforgeUserInfo = $this->getGForgeUserInfo();
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
            $gforgeProjectInfo = $this->getGForgeProjectInformation($projectName);
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
            $gforgeProjectRights = $this->getGForgeProjectRights($projectName, $this->app->userInfo['external_id']);
            $this->userMgr->setUserProjectRights($projectInfo['id'], $this->app->userInfo['id'], $gforgeProjectRights);
        }
    }

    function getGForgeUserInfo() {
        return ['displayname' => 'Mann Fred',
                'username' => 'mfred',
                'email' => 'mfred@example.org',
                'image_url' => 'http://example.org/mfredImage'];
    }

    function getGForgeProjectInformation($projectname) {
        return ['name' => 'demoProjectPublic',
                'title' => 'GForge Demo Project',
                'is_public_viewable' => 1];
    }

    function getGForgeProjectRights($projectname, $username) {
        return ['can_read' => 0,
                'is_owner' => 1,
                'can_write' => 0];
    }
}