 <?php

class GForgeApiFake {

    public function getGForgeUserInfo() {
        return ['displayname' => 'Mann Fred',
                'username' => 'mfred',
                'email' => 'mfred@example.org',
                'image_url' => 'http://example.org/mfredImage'];
    }
    
    public function getGForgeProjectInformation($projectname) {
        return ['name' => 'demoProjectPublic',
                'title' => 'GForge Demo Project',
                'is_public_viewable' => 1];
    }

    public function getGForgeProjectRights($projectname) {
        return ['can_read' => 1,
                'is_owner' => 1,
                'can_write' => 1];
    }
}