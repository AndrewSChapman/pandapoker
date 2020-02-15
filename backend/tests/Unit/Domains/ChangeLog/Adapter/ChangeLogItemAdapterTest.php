<?php

namespace Testing\Unit\Domains\ChangeLog\Adapter;

use App\Domains\ChangeLog\Adapter\ChangeLogItemAdapter;
use App\Domains\ChangeLog\Type\ChangeLogId;
use App\Domains\Room\Event\RoomCreatedEvent;
use App\Domains\Shared\Exception\AdapterException;
use PhpTypes\Exception\ConstraintException;
use Testing\Unit\UnitTest;

class ChangeLogItemAdapterTest extends UnitTest
{
    public function testChangeLogItemCanBeCreatedFromValidArray(): void
    {
        $user = $this->getDataHelper()->user()->makeUser();
        $room = $this->getDataHelper()->room()->makeRoom($user);

        $event = new RoomCreatedEvent($room, $user->getId());
        $changLogId = new ChangeLogId(1);

        $validChangeLogData = [
            'id' => $changLogId->getValue(),
            'event_data' => $event->toArray()
        ];

        $changeLogItem = ChangeLogItemAdapter::fromArray($validChangeLogData);

        $this->assertEquals($changLogId->getValue(), $changeLogItem->getId()->getValue());
    }

    /** @dataProvider invalidDataProvider */
    public function testChangeLogItemAdapterWillThrowExceptionIfDataInvalid(array $invalidData): void
    {
        try {
            $changeLogItem = ChangeLogItemAdapter::fromArray($invalidData);
            $this->assertTrue(false);
        } catch (ConstraintException $exception) {
            $this->assertTrue(true);
        } catch (AdapterException $exception) {
            $this->assertTrue(true);
        }
    }

    public function invalidDataProvider(): array
    {
        return [
            [
                [
                    'id' => -1,
                    'event_data' => [
                        'event_id' => '369faba9-2d05-4a13-9978-008bd5f5ccfc',
                        'event_name' => 'ROOM_CREATED',
                        'event_created_at' => 12345
                    ]
                ]
            ],
            [
                [
                    'id' => 1,
                ]
            ],
            [
                [
                    'id' => 1,
                    'event_data' => [
                        'event_name' => 'ROOM_CREATED',
                        'event_created_at' => 12345
                    ]
                ]
            ],
            [
                [
                    'id' => 1,
                    'event_data' => [
                        'event_id' => '369faba9-2d05-4a13-9978-008bd5f5ccfc',
                        'event_created_at' => 12345
                    ]
                ]
            ],
            [
                [
                    'id' => 1,
                    'event_data' => [
                        'event_id' => '369faba9-2d05-4a13-9978-008bd5f5ccfc',
                        'event_name' => 'ROOM_CREATED',
                    ]
                ]
            ]
        ];
    }
}
