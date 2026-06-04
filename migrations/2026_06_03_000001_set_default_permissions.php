<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'moderateSignature' => Group::MODERATOR_ID,
    'haveSignature' => Group::MEMBER_ID,
]);
