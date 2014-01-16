<?php

class GForgeApi {

    public function getGForgeUserInfo() {
        if (! session_loggedin()) 
            return Null;
        
        $user = session_get_user();
        return ['displayname' => $user->getRealName(),
                'username' => $user->getUnixName(),
                'email' =>  $user->getEmail(),
                'image_url' => ''];
    }
    
    public function getGForgeProjectInformation($projectname) {
        $group = group_get_object_by_name($projectname);
        
        return ['name' => $group->getUnixName(),
                'title' => $group->getPublicName(),
                'is_public_viewable' =>  ($group->isPublic() ? 1 : 0)];
    }

    public function getGForgeProjectRights($projectname) {
        $group = group_get_object_by_name($projectname);
        
        $user = session_get_user();
        $glist = $user->getGroups();
        $userIsProjectMember = false;
        foreach ($glist as $g) {
            if ($g->getID() == $group->getID()) {
                $userIsProjectMember = true;
                break;
            }
        }

        return ['can_read' => $userIsProjectMember,
                'is_owner' => 0,
                'can_write' => $userIsProjectMember];
    }
}