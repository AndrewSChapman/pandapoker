<?php

namespace Testing\Unit\Domains\User\Collection;

use App\Domains\User\Collection\UserList;
use App\Domains\User\Entity\User;
use App\Domains\User\Type\Username;
use Testing\Unit\UnitTest;

class UserListTest extends UnitTest
{
    public function testUserListWillSortByUsername(): void
    {
        $userList = new UserList();
        $userList->add($this->getDataHelper()->user()->makeUser(null, new Username('MM')));
        $userList->add($this->getDataHelper()->user()->makeUser(null, new Username('ZZ')));
        $userList->add($this->getDataHelper()->user()->makeUser(null, new Username('BB')));
        $userList->add($this->getDataHelper()->user()->makeUser(null, new Username('TT')));
        $userList->add($this->getDataHelper()->user()->makeUser(null, new Username('AA')));

        $userList->sortByUsername();

        $this->assertCount(5, $userList);

        $expectedOrder = ['AA', 'BB', 'MM', 'TT', 'ZZ'];

        $actualOrder = array_map(function(User $user) {
            return $user->getUsername()->toString();
        }, $userList->toArray());

        $this->assertEquals($expectedOrder, $actualOrder);
    }
}
